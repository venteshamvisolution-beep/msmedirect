<?php

namespace App\Console\Commands;

use App\Models\BusinessEmailCampaign;
use App\Models\BusinessEmailCampaignItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncMailgunEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailgun:sync-events {--limit=300 : Number of events to fetch} {--hours= : Legacy option for backward compatibility}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync email delivery and bounce events directly from Mailgun API';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $domain = config('services.mailgun.domain') ?: env('MAILGUN_DOMAIN');
        $secret = config('services.mailgun.secret') ?: env('MAILGUN_SECRET');
        $endpoint = config('services.mailgun.endpoint') ?: env('MAILGUN_ENDPOINT', 'api.mailgun.net');

        if (empty($domain) || empty($secret)) {
            $this->error('Mailgun domain or secret is not configured in .env / config.');
            return 1;
        }

        $limit = (int) $this->option('limit');
        $this->info("Fetching latest {$limit} Mailgun event(s) for domain '{$domain}'...");

        $apiUrl = "https://{$endpoint}/v3/{$domain}/events";

        $response = Http::withBasicAuth('api', $secret)
            ->get($apiUrl, [
                'limit' => $limit,
            ]);

        if ($response->failed()) {
            $this->error("Mailgun API Error ({$response->status()}): " . $response->body());
            Log::error("Mailgun API Event Sync Failed: " . $response->body());
            return 1;
        }

        $events = $response->json('items', []);
        $this->info("Retrieved " . count($events) . " event(s) from Mailgun.");

        $updatedFailed = 0;
        $updatedDelivered = 0;

        // Process events in chronological order (oldest first) so recent status overwrites older
        $events = array_reverse($events);

        foreach ($events as $eventData) {
            $event = strtolower($eventData['event'] ?? '');

            // Only care about delivered, failed, permanent_fail, bounced, dropped, rejected
            if (!in_array($event, ['delivered', 'failed', 'permanent_fail', 'bounced', 'dropped', 'rejected'])) {
                continue;
            }

            $userVariables = $eventData['user-variables'] ?? [];
            $campaignItemId = $userVariables['campaign_item_id'] ?? null;
            $recipient = strtolower(trim($eventData['recipient'] ?? ''));
            $messageId = $eventData['message']['headers']['message-id'] ?? null;

            $deliveryStatus = $eventData['delivery-status'] ?? [];
            $reason = $deliveryStatus['message']
                ?? $deliveryStatus['description']
                ?? $eventData['reason']
                ?? ('Mailgun Event: ' . $event);

            // Locate campaign item
            $item = null;

            if ($campaignItemId) {
                $item = BusinessEmailCampaignItem::find($campaignItemId);
            }

            if (!$item && $messageId) {
                $item = BusinessEmailCampaignItem::where('message_id', $messageId)->first();
            }

            if (!$item && !empty($recipient)) {
                $item = BusinessEmailCampaignItem::whereRaw('LOWER(email) = ?', [$recipient])
                    ->latest()
                    ->first();
            }

            if (!$item) {
                continue;
            }

            $oldStatus = $item->status;
            $campaign = $item->campaign;

            // Process Failure / Bounce
            if (in_array($event, ['failed', 'permanent_fail', 'bounced', 'dropped', 'rejected'])) {
                if ($oldStatus !== 'failed') {
                    $item->update([
                        'status' => 'failed',
                        'delivery_status' => 'failed',
                        'failed_at' => now(),
                        'error_message' => 'Mailgun Failure: ' . substr($reason, 0, 1000),
                    ]);

                    if ($campaign) {
                        if ($oldStatus === 'sent' && $campaign->sent_count > 0) {
                            $campaign->decrement('sent_count');
                        }
                        if ($oldStatus === 'delivered' && $campaign->delivered_count > 0) {
                            $campaign->decrement('delivered_count');
                        }
                        $campaign->increment('failed_count');
                        $this->recalculateCampaignStatus($campaign);
                    }

                    $updatedFailed++;
                    $this->warn(" -> Item #{$item->id} ({$item->email}) updated to FAILED: {$reason}");
                }
            }
            // Process Delivered
            elseif ($event === 'delivered') {
                if ($oldStatus !== 'delivered') {
                    $item->update([
                        'status' => 'delivered',
                        'delivery_status' => 'delivered',
                        'delivered_at' => now(),
                    ]);

                    if ($campaign) {
                        if ($oldStatus === 'sent' && $campaign->sent_count > 0) {
                            $campaign->decrement('sent_count');
                        }
                        if ($oldStatus === 'failed' && $campaign->failed_count > 0) {
                            $campaign->decrement('failed_count');
                        }
                        $campaign->increment('delivered_count');
                        $this->recalculateCampaignStatus($campaign);
                    }

                    $updatedDelivered++;
                    $this->info(" -> Item #{$item->id} ({$item->email}) updated to DELIVERED");
                }
            }
        }

        $this->info("Sync completed! Updated {$updatedDelivered} as Delivered, {$updatedFailed} as Failed.");
        return 0;
    }

    protected function recalculateCampaignStatus(BusinessEmailCampaign $campaign): void
    {
        $fresh = $campaign->fresh();
        if (!$fresh) return;

        $processed = $fresh->delivered_count + $fresh->failed_count;
        if ($fresh->queued_count > 0 && $processed >= $fresh->queued_count) {
            $status = ($fresh->failed_count >= $fresh->queued_count && $fresh->delivered_count === 0) ? 'failed' : 'completed';
            $fresh->update([
                'status' => $status,
                'completed_at' => $fresh->completed_at ?? now(),
            ]);
        }
    }
}
