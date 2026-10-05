<?php

namespace App\Filament\Resources\MsmeBusinessDataResource\Pages;

use App\Filament\Resources\MsmeBusinessDataResource;
use App\Jobs\StartBusinessEmailCampaign;
use App\Models\BusinessEmailCampaign;
use App\Models\MsmeBusinessData;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

use Filament\Forms;

class ListMsmeBusinessData extends ListRecords
{
    protected static string $resource = MsmeBusinessDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('sendEmailCampaign')
                ->label('Start Automated Campaign')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->modalHeading('Start Automated Batch Email Campaign')
                ->modalDescription(function () {
                    $minId = MsmeBusinessData::query()->whereNotNull('email')->where('email', '!=', '')->min('id') ?? 1;
                    $maxId = MsmeBusinessData::query()->whereNotNull('email')->where('email', '!=', '')->max('id') ?? 1;
                    $total = MsmeBusinessData::query()->whereNotNull('email')->where('email', '!=', '')->count();
                    return "Total eligible database records: {$total} (ID Range: {$minId} to {$maxId}). The system will process emails in automated batches (e.g. 1-1000, 1001-2000, etc.) until all records up to Max Target ID are completed. Already sent emails and registered user_data emails will be automatically skipped.";
                })
                ->form([
                    Forms\Components\TextInput::make('start_id')
                        ->label('Start Business Data ID')
                        ->placeholder('e.g. 1')
                        ->numeric()
                        ->minValue(1)
                        ->default(fn() => MsmeBusinessData::query()->whereNotNull('email')->where('email', '!=', '')->min('id') ?? 1)
                        ->required()
                        ->helperText('Initial starting record ID.'),

                    Forms\Components\TextInput::make('batch_size')
                        ->label('Batch Size (Emails per Batch)')
                        ->placeholder('e.g. 1000')
                        ->numeric()
                        ->minValue(2)
                        ->default(1000)
                        ->required()
                        ->helperText('Number of records to process per batch (e.g. 1000).'),

                    Forms\Components\TextInput::make('max_id')
                        ->label('Max Target ID (Optional Stop ID)')
                        ->placeholder('Leave empty for highest ID in database')
                        ->numeric()
                        ->default(fn() => MsmeBusinessData::query()->whereNotNull('email')->where('email', '!=', '')->max('id'))
                        ->helperText('Leave empty or set to highest ID (e.g. 100000).'),
                ])
                ->modalSubmitActionLabel('Start Automated Campaign')
                ->action(function (array $data) {
                    $startId = (int) $data['start_id'];
                    $batchSize = (int) $data['batch_size'];
                    $dbMaxId = MsmeBusinessData::query()->whereNotNull('email')->where('email', '!=', '')->max('id') ?? $startId;
                    $maxId = !empty($data['max_id']) ? min((int) $data['max_id'], $dbMaxId) : $dbMaxId;

                    if ($startId > $maxId) {
                        Notification::make()
                            ->warning()
                            ->title('Invalid Parameters')
                            ->body('Start ID cannot be greater than Max Target ID.')
                            ->send();
                        return;
                    }

                    $endId = min($startId + $batchSize - 1, $maxId);

                    // Prevent concurrent active processing campaigns
                    $runningCampaign = BusinessEmailCampaign::where('status', 'processing')->first();
                    if ($runningCampaign) {
                        Notification::make()
                            ->warning()
                            ->title('Campaign Already Running')
                            ->body("Campaign '{$runningCampaign->name}' is currently processing. Please wait for it to complete.")
                            ->send();
                        return;
                    }

                    $campaignName = "Automated Campaign (IDs {$startId} - {$endId}) - " . now()->format('Y-m-d H:i');
                    $subject = 'GROW YOUR BUSINESS WITH MSME DIRECT';

                    // Create the initial campaign batch record
                    $campaign = BusinessEmailCampaign::create([
                        'name' => $campaignName,
                        'subject' => $subject,
                        'content' => null,
                        'start_id' => $startId,
                        'end_id' => $endId,
                        'batch_size' => $batchSize,
                        'max_id' => $maxId,
                        'status' => 'pending',
                    ]);

                    // Dispatch initial batch job to Redis queue
                    StartBusinessEmailCampaign::dispatch($campaign->id);

                    Notification::make()
                        ->success()
                        ->title('Automated Campaign Started')
                        ->body("Automated batch campaign initialized for IDs {$startId} to {$endId} (Batch Size: {$batchSize}, Max ID: {$maxId}). Next batches will run automatically in the background.")
                        ->send();
                }),
        ];
    }
}
