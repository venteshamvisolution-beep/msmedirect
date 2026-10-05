<?php

namespace App\Http\Controllers;

use App\Models\BusinessEmailCampaign;
use App\Models\BusinessEmailCampaignItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MailgunWebhookController extends Controller
{
    /**
     * Handle incoming Mailgun webhook events (delivered, failed, permanent_fail, bounced, dropped, etc.).
     */
    public function handle(Request $request): JsonResponse
    {
        Log::info("Mailgun Webhook Received: ", $request->all());

        $payload = $request->input('event-data', $request->all());

        $event = strtolower($payload['event'] ?? $request->input('event', ''));
        $recipient = strtolower(trim($payload['recipient'] ?? $request->input('recipient', '')));

        $userVariables = $payload['user-variables'] ?? [];
        $campaignItemId = $userVariables['campaign_item_id'] ?? null;
        $messageId = $payload['message']['headers']['message-id'] ?? null;

        $deliveryStatus = $payload['delivery-status'] ?? [];
        $reason = $deliveryStatus['message']
            ?? $deliveryStatus['description']
            ?? $payload['reason']
            ?? ('Mailgun Webhook Event: ' . $event);

        // 1. Locate the target campaign items
        $campaignItems = collect();

        if ($campaignItemId) {
            $item = BusinessEmailCampaignItem::find($campaignItemId);
            if ($item) {
                $campaignItems->push($item);
            }
        }

        if ($campaignItems->isEmpty() && $messageId) {
            $item = BusinessEmailCampaignItem::where('message_id', $messageId)->first();
            if ($item) {
                $campaignItems->push($item);
            }
        }

        if ($campaignItems->isEmpty() && !empty($recipient)) {
            $campaignItems = BusinessEmailCampaignItem::whereRaw('LOWER(email) = ?', [$recipient])
                ->latest()
                ->take(5)
                ->get();
        }

        if ($campaignItems->isEmpty()) {
            Log::warning("Mailgun Webhook: No matching CampaignItem found for recipient '{$recipient}', event '{$event}'");
            return response()->json(['status' => 'ignored'], 200);
        }

        foreach ($campaignItems as $item) {
            $oldStatus = $item->status;
            $campaign = $item->campaign;

            // Handle Failure / Bounce / Rejection events
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

                    Log::warning("Updated CampaignItem ID {$item->id} ({$item->email}) to FAILED: {$reason}");
                }
            }
            // Handle Delivery Success events
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

                    Log::info("Updated CampaignItem ID {$item->id} ({$item->email}) to DELIVERED");
                }
            }
        }

        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Recalculate campaign status when items are updated.
     */
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
