<?php

namespace App\Filament\Resources\BusinessEmailCampaignResource\Pages;

use App\Filament\Resources\BusinessEmailCampaignResource;
use Filament\Resources\Pages\ListRecords;

use App\Models\BusinessEmailCampaign;

use Filament\Actions;

class ListBusinessEmailCampaigns extends ListRecords
{
    protected static string $resource = BusinessEmailCampaignResource::class;

    public function mount(): void
    {
        parent::mount();

        // Automatically fetch latest Mailgun delivery status on page load
        try {
            \Illuminate\Support\Facades\Artisan::call('mailgun:sync-events', ['--limit' => 300]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Auto Mailgun Sync Error: " . $e->getMessage());
        }

        // Auto-heal/update campaign status based on item delivery counts
        BusinessEmailCampaign::all()
            ->each(function ($campaign) {
                $processed = $campaign->sent_count + $campaign->delivered_count + $campaign->failed_count;
                if ($campaign->queued_count > 0 && $processed >= $campaign->queued_count) {
                    $status = ($campaign->failed_count >= $campaign->queued_count && $campaign->delivered_count === 0) ? 'failed' : 'completed';
                    if ($campaign->status !== $status) {
                        $campaign->update([
                            'status' => $status,
                            'completed_at' => $campaign->completed_at ?? now(),
                        ]);
                    }
                } elseif ($campaign->queued_count === 0 && $campaign->total_count > 0 && $campaign->status !== 'completed') {
                    $campaign->update([
                        'status' => 'completed',
                        'completed_at' => $campaign->completed_at ?? now(),
                    ]);
                }
            });
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
