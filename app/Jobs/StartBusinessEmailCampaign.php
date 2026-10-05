<?php

namespace App\Jobs;

use App\Models\BusinessEmailCampaign;
use App\Models\BusinessEmailCampaignItem;
use App\Models\MsmeBusinessData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class StartBusinessEmailCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $campaignId;
    public ?int $startId;
    public ?int $endId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $campaignId, ?int $startId = null, ?int $endId = null)
    {
        $this->campaignId = $campaignId;
        $this->startId = $startId;
        $this->endId = $endId;
        $this->onQueue('business-campaigns');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $campaign = BusinessEmailCampaign::find($this->campaignId);

        if (!$campaign) {
            Log::error("Campaign ID {$this->campaignId} not found.");
            return;
        }

        if (in_array($campaign->status, ['completed', 'cancelled']) && $campaign->queued_count > 0) {
            Log::warning("Campaign ID {$this->campaignId} is already {$campaign->status}. Aborting.");
            return;
        }

        // Initialize range parameters from campaign record if available
        $startId = $campaign->start_id ?? $this->startId;
        $endId = $campaign->end_id ?? $this->endId;
        $batchSize = $campaign->batch_size ?? ($startId && $endId ? ($endId - $startId + 1) : 1000);
        $maxId = $campaign->max_id;

        Log::info("Starting campaign batch dispatch for Campaign ID: {$campaign->id} (Range: " . ($startId ? "{$startId}-{$endId}" : "All") . ")");

        $campaign->update([
            'status' => 'processing',
            'started_at' => $campaign->started_at ?? now(),
            'error_log' => null,
        ]);

        try {
            $totalCount = 0;
            $skippedCount = 0;
            $queuedCount = 0;

            $query = MsmeBusinessData::query()
                ->whereNotNull('email')
                ->where('email', '!=', '');

            if (!empty($startId) && !empty($endId)) {
                $query->whereBetween('id', [$startId, $endId]);
            }

            // Chunk records in memory-safe batches of 500
            $query->chunkById(500, function ($records) use ($campaign, &$totalCount, &$skippedCount, &$queuedCount) {

                // Gather emails for bulk duplicate checks
                $batchEmails = $records->map(fn($r) => trim($r->email))->filter()->unique()->values()->all();

                // 1. Check already registered emails in user_data table
                $registeredEmails = \Illuminate\Support\Facades\DB::table('user_data')
                    ->whereIn('email', $batchEmails)
                    ->pluck('email')
                    ->map(fn($e) => strtolower(trim($e)))
                    ->toArray();

                // 2. Check already sent emails across ALL campaign items
                $sentEmails = \Illuminate\Support\Facades\DB::table('business_email_campaign_items')
                    ->where('status', 'sent')
                    ->whereIn('email', $batchEmails)
                    ->pluck('email')
                    ->map(fn($e) => strtolower(trim($e)))
                    ->toArray();

                $excludedEmails = array_flip(array_unique(array_merge($registeredEmails, $sentEmails)));

                foreach ($records as $record) {
                    $email = strtolower(trim($record->email));

                    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        continue;
                    }

                    $totalCount++;

                    // SKIP IF ALREADY REGISTERED OR ALREADY SENT
                    if (isset($excludedEmails[$email])) {
                        $skippedCount++;
                        continue;
                    }

                    try {
                        // Prevent duplicate campaign item creation
                        $campaignItem = BusinessEmailCampaignItem::firstOrCreate(
                            [
                                'campaign_id' => $campaign->id,
                                'business_data_id' => $record->id,
                            ],
                            [
                                'email' => $record->email,
                                'status' => 'queued',
                                'attempts' => 0,
                            ]
                        );

                        if ($campaignItem->status === 'sent') {
                            $skippedCount++;
                            continue;
                        }

                        // Dispatch individual email job
                        SendBusinessDataEmail::dispatch($record->id, $campaignItem->id)
                            ->onQueue('business-emails');

                        $queuedCount++;
                    } catch (\Throwable $e) {
                        Log::error("Error processing campaign item for BusinessData ID {$record->id}: " . $e->getMessage());
                    }
                }

                // Update campaign counts after chunk
                $campaign->update([
                    'total_count' => $totalCount,
                    'skipped_count' => $skippedCount,
                    'queued_count' => $queuedCount,
                    'pending_count' => max(0, $queuedCount - ($campaign->sent_count + $campaign->failed_count)),
                ]);
            });

            $campaign->fresh();
            $processed = $campaign->sent_count + $campaign->failed_count;

            $campaign->update([
                'total_count' => $totalCount,
                'skipped_count' => $skippedCount,
                'queued_count' => $queuedCount,
                'pending_count' => max(0, $queuedCount - $processed),
            ]);

            if ($queuedCount === 0) {
                $campaign->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
                Log::info("Campaign ID {$campaign->id} finished dispatching: 0 eligible recipients found (Skipped: {$skippedCount}).");
            } elseif ($queuedCount > 0 && $processed >= $queuedCount && $campaign->status === 'processing') {
                $campaign->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
                Log::info("Campaign ID {$campaign->id} completed! All {$queuedCount} queued emails sent.");
            } else {
                Log::info("Campaign ID {$campaign->id} successfully queued {$queuedCount} emails (Total: {$totalCount}, Skipped: {$skippedCount}).");
            }

            // =========================================================================
            // AUTOMATIC BATCH CHAINING (AUTO-START NEXT BATCH)
            // =========================================================================
            if ($startId && $endId && $batchSize) {
                $dbMaxId = MsmeBusinessData::max('id') ?? $endId;
                $targetMaxId = $maxId ? min($maxId, $dbMaxId) : $dbMaxId;

                $nextStartId = $endId + 1;

                if ($nextStartId <= $targetMaxId) {
                    $nextEndId = min($nextStartId + $batchSize - 1, $targetMaxId);

                    // Check if there are eligible records in the next range
                    $hasMoreRecords = MsmeBusinessData::whereBetween('id', [$nextStartId, $nextEndId])
                        ->whereNotNull('email')
                        ->where('email', '!=', '')
                        ->exists();

                    if ($hasMoreRecords) {
                        $parentId = $campaign->parent_id ?: $campaign->id;

                        $nextCampaign = BusinessEmailCampaign::create([
                            'parent_id' => $parentId,
                            'start_id' => $nextStartId,
                            'end_id' => $nextEndId,
                            'batch_size' => $batchSize,
                            'max_id' => $maxId,
                            'name' => "Automated Batch (IDs {$nextStartId} - {$nextEndId}) - " . now()->format('Y-m-d H:i'),
                            'subject' => $campaign->subject,
                            'content' => $campaign->content,
                            'status' => 'pending',
                        ]);

                        // Automatically dispatch next batch to Redis queue
                        self::dispatch($nextCampaign->id)->onQueue('business-campaigns');

                        Log::info("AUTO-CHAINING: Dispatched next batch Campaign ID {$nextCampaign->id} for IDs {$nextStartId} - {$nextEndId}.");
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error("Failed campaign batch processing for Campaign ID {$campaign->id}: " . $e->getMessage());

            $campaign->update([
                'status' => 'failed',
                'error_log' => "Error: " . $e->getMessage() . "\n\nTrace:\n" . $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
