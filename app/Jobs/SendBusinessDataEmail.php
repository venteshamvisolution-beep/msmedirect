<?php

namespace App\Jobs;

use App\Mail\BusinessDataMail;
use App\Models\BusinessEmailCampaign;
use App\Models\BusinessEmailCampaignItem;
use App\Models\MsmeBusinessData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBusinessDataEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $businessDataId;
    public int $campaignItemId;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [10, 30, 60];
    }

    /**
     * Create a new job instance.
     */
    public function __construct(int $businessDataId, int $campaignItemId)
    {
        $this->businessDataId = $businessDataId;
        $this->campaignItemId = $campaignItemId;
        $this->onQueue('business-emails');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Processing email job for BusinessData ID: {$this->businessDataId}, CampaignItem ID: {$this->campaignItemId}");

        $campaignItem = BusinessEmailCampaignItem::with('campaign')->find($this->campaignItemId);

        if (!$campaignItem) {
            Log::warning("CampaignItem ID {$this->campaignItemId} not found. Aborting job.");
            return;
        }

        // Prevent duplicate sending if already marked sent
        if ($campaignItem->status === 'sent') {
            Log::info("CampaignItem ID {$this->campaignItemId} already sent. Skipping.");
            return;
        }

        $businessData = MsmeBusinessData::find($this->businessDataId);
        $email = $campaignItem->email ?: ($businessData ? trim($businessData->email) : null);

        if (!$businessData || empty($email)) {
            Log::warning("BusinessData ID {$this->businessDataId} or email is missing.");
            $this->markAsFailed($campaignItem, 'Business data record or email is missing.');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Log::warning("Invalid email address format for BusinessData ID {$this->businessDataId}: {$email}");
            $this->markAsFailed($campaignItem, "Invalid email address format: {$email}");
            return;
        }

        // Mark status as sending and increment attempt counter
        $campaignItem->update([
            'status' => 'sending',
            'attempts' => $campaignItem->attempts + 1,
        ]);

        try {
            $campaign = $campaignItem->campaign;
            $subject = $campaign->subject ?? 'Business Opportunity with MSME Direct';
            $content = $campaign->content ?? null;

            // Send Email using Mailgun / Mailable setup
            $mailable = new BusinessDataMail($businessData, $subject, $content, $campaignItem->id);
            $mailer = !empty(config('services.mailgun.secret')) ? Mail::mailer('mailgun') : Mail::mailer();
            $sentMessage = $mailer->to($email)->send($mailable);

            // Extract message ID if returned by transport
            $messageId = null;
            if ($sentMessage && method_exists($sentMessage, 'getMessageId')) {
                $messageId = $sentMessage->getMessageId();
            }

            // Mark item as sent
            $campaignItem->update([
                'status' => 'sent',
                'delivery_status' => 'sent',
                'message_id' => $messageId,
                'sent_at' => now(),
                'error_message' => null,
            ]);

            // Update campaign counts
            if ($campaign) {
                $campaign->increment('sent_count');
                $this->checkCampaignCompletion($campaign);
            }

            Log::info("Successfully sent email to {$email} for CampaignItem ID {$this->campaignItemId}");
        } catch (\Throwable $e) {
            Log::error("Error sending email to {$email} for CampaignItem ID {$this->campaignItemId}: " . $e->getMessage());

            if ($this->attempts() >= $this->tries) {
                $this->markAsFailed($campaignItem, $e->getMessage());
            } else {
                $campaignItem->update([
                    'error_message' => 'Attempt ' . $this->attempts() . ' failed: ' . substr($e->getMessage(), 0, 500),
                ]);
            }

            throw $e; // Re-throw to allow queue retry and backoff
        }
    }

    protected function markAsFailed(BusinessEmailCampaignItem $campaignItem, string $errorMessage): void
    {
        $campaignItem->update([
            'status' => 'failed',
            'failed_at' => now(),
            'error_message' => substr($errorMessage, 0, 1000),
        ]);

        $campaign = $campaignItem->campaign;
        if ($campaign) {
            $campaign->increment('failed_count');
            $this->checkCampaignCompletion($campaign);
        }
    }

    protected function checkCampaignCompletion(BusinessEmailCampaign $campaign): void
    {
        $freshCampaign = $campaign->fresh();
        if (!$freshCampaign) {
            return;
        }

        $processed = $freshCampaign->sent_count + $freshCampaign->delivered_count + $freshCampaign->failed_count;
        $targetCount = $freshCampaign->queued_count;

        if (in_array($freshCampaign->status, ['processing', 'pending'])) {
            if ($targetCount > 0 && $processed >= $targetCount) {
                $status = ($freshCampaign->failed_count >= $targetCount && $freshCampaign->delivered_count === 0) ? 'failed' : 'completed';
                $freshCampaign->update([
                    'status' => $status,
                    'completed_at' => now(),
                ]);
                Log::info("Campaign ID {$freshCampaign->id} finished! Status: {$status}. Queued: {$targetCount}, Sent: {$freshCampaign->sent_count}, Delivered: {$freshCampaign->delivered_count}, Failed: {$freshCampaign->failed_count}");
            } elseif ($targetCount === 0 && $freshCampaign->total_count > 0) {
                $freshCampaign->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
                Log::info("Campaign ID {$freshCampaign->id} completed: All records skipped.");
            }
        }
    }
}
