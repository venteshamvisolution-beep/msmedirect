<?php

namespace App\Filament\Resources\MsmeBusinessDataResource\Pages;

use App\Filament\Resources\MsmeBusinessDataResource;
use App\Jobs\StartBusinessEmailCampaign;
use App\Models\BusinessEmailCampaign;
use App\Models\MsmeBusinessData;
use App\Models\UserData;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ListMsmeBusinessData extends ListRecords
{
    protected static string $resource = MsmeBusinessDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('registerUsers')
                ->label('Auto Register Users')
                ->icon('heroicon-o-user-plus')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Auto Register Business Users in User Data')
                ->modalDescription(function () {
                    $totalMsme = MsmeBusinessData::count();
                    $registeredMsmeCount = UserData::whereNotNull('msme_business_data_id')->count();
                    $unregisteredCount = max(0, $totalMsme - $registeredMsmeCount);
                    return "Identify and register all Business Data records that do not currently exist in the User Data table (Estimated unregistered: {$unregisteredCount} of {$totalMsme}). Records that already exist will be skipped automatically to prevent duplicate users.";
                })
                ->modalSubmitActionLabel('Register Users')
                ->action(function () {
                    ini_set('memory_limit', '1024M');
                    set_time_limit(0);
                    DB::disableQueryLog();

                    // Check which optional columns exist in user_data table on the database
                    $hasCompanyCol = Schema::hasColumn('user_data', 'company');
                    $hasPersonNameCol = Schema::hasColumn('user_data', 'person_name');
                    $hasOfficeNoCol = Schema::hasColumn('user_data', 'office_no');
                    $hasMobileNoCol = Schema::hasColumn('user_data', 'mobile_no');
                    $hasWebsiteCol = Schema::hasColumn('user_data', 'website');
                    $hasDetailsCol = Schema::hasColumn('user_data', 'details');

                    // Pre-fetch all existing UserData msme_business_data_ids and emails into hash sets for instant O(1) checks
                    $existingMsmeIds = UserData::query()
                        ->whereNotNull('msme_business_data_id')
                        ->pluck('msme_business_data_id', 'msme_business_data_id')
                        ->toArray();

                    $existingEmails = [];
                    foreach (UserData::query()->whereNotNull('email')->where('email', '!=', '')->pluck('email') as $eVal) {
                        $existingEmails[strtolower(trim((string) $eVal))] = true;
                    }

                    $businessRecords = MsmeBusinessData::all();

                    $registeredCount = 0;
                    $skippedCount = 0;
                    $failedCount = 0;
                    $errors = [];
                    $processedEmailsInBatch = [];

                    DB::beginTransaction();
                    try {
                        foreach ($businessRecords as $bData) {
                            // Check 1: Skip if msme_business_data_id already registered
                            if (isset($existingMsmeIds[$bData->id])) {
                                $skippedCount++;
                                continue;
                            }

                            // Check 2: Skip if email already exists in UserData or repeated in batch
                            $email = !empty($bData->email) ? strtolower(trim((string) $bData->email)) : null;
                            if (!empty($email)) {
                                if (isset($existingEmails[$email]) || isset($processedEmailsInBatch[$email])) {
                                    $skippedCount++;
                                    continue;
                                }
                                $processedEmailsInBatch[$email] = true;
                            }

                            $company = !empty($bData->company) ? trim((string) $bData->company) : null;
                            $personName = !empty($bData->person_name) ? trim((string) $bData->person_name) : null;
                            $address = !empty($bData->address) ? trim((string) $bData->address) : (!empty($bData->add) ? trim((string) $bData->add) : null);
                            $mobileNo = !empty($bData->mobile_no) ? trim((string) $bData->mobile_no) : null;
                            $officeNo = !empty($bData->office_no) ? trim((string) $bData->office_no) : null;
                            $contactNo = $mobileNo ?? $officeNo ?? '0000000000';
                            $city = !empty($bData->city) ? trim((string) $bData->city) : '';
                            $state = !empty($bData->state) ? trim((string) $bData->state) : '';
                            $pincode = !empty($bData->pincode) && is_numeric($bData->pincode) ? (int) $bData->pincode : 0;
                            $website = !empty($bData->website) ? trim((string) $bData->website) : null;
                            $details = !empty($bData->details) ? trim((string) $bData->details) : null;

                            $userDataPayload = [
                                'msme_business_data_id' => $bData->id,
                                'is_bulk_user' => true,
                                'organization_name' => $company ?? $personName ?? 'N/A',
                                'email' => $bData->email,
                                'contact_no' => $contactNo,
                                'address' => $address,
                                'city' => $city,
                                'state' => $state,
                                'pincode' => $pincode,
                                'business_type' => 0,
                                'type_organization' => 'MSME',
                                'msme_type' => 'Micro',
                                'business_category' => 1,
                                'specialization' => 'General',
                                'experience' => 1,
                                'document_no' => 'N/A',
                                'document_file' => [],
                                'state_city' => !empty($state) ? $state : 'All',
                                'business_size' => ['micro'],
                                'approve' => 1,
                                'want_to' => 'Connect',
                            ];

                            if ($hasCompanyCol) $userDataPayload['company'] = $company;
                            if ($hasPersonNameCol) $userDataPayload['person_name'] = $personName;
                            if ($hasOfficeNoCol) $userDataPayload['office_no'] = $officeNo;
                            if ($hasMobileNoCol) $userDataPayload['mobile_no'] = $mobileNo;
                            if ($hasWebsiteCol) $userDataPayload['website'] = $website;
                            if ($hasDetailsCol) $userDataPayload['details'] = $details;

                            try {
                                UserData::create($userDataPayload);

                                $existingMsmeIds[$bData->id] = true;
                                $registeredCount++;
                            } catch (\Throwable $e) {
                                $failedCount++;
                                $errors[] = "Business Data ID #{$bData->id}: " . $e->getMessage();
                            }
                        }

                        DB::commit();
                    } catch (\Throwable $e) {
                        DB::rollBack();
                        Notification::make()
                            ->danger()
                            ->title('Registration Error')
                            ->body('Failed to register users: ' . $e->getMessage())
                            ->send();
                        return;
                    }

                    // Build Notification Summary
                    if ($registeredCount === 0 && $failedCount === 0 && $skippedCount > 0) {
                        Notification::make()
                            ->info()
                            ->title('All Records Already Exist')
                            ->body("All {$skippedCount} records already exist in the User Data module. No new records were registered.")
                            ->send();
                    } elseif ($failedCount === 0) {
                        $summaryMsg = "Successfully registered {$registeredCount} users into User Data.";
                        if ($skippedCount > 0) {
                            $summaryMsg .= " {$skippedCount} records skipped (already exist in User Data).";
                        }

                        Notification::make()
                            ->success()
                            ->title('User Registration Completed')
                            ->body($summaryMsg)
                            ->send();
                    } else {
                        $summaryMsg = "Successfully registered {$registeredCount} users into User Data.";
                        if ($skippedCount > 0) {
                            $summaryMsg .= " {$skippedCount} records skipped (already exist in User Data).";
                        }
                        $summaryMsg .= " {$failedCount} records failed.";

                        $errSummary = "\n\nErrors:\n" . implode("\n", array_slice($errors, 0, 5));
                        if (count($errors) > 5) {
                            $errSummary .= "\n...and " . (count($errors) - 5) . " more errors.";
                        }

                        Notification::make()
                            ->warning()
                            ->title('User Registration Completed with Warnings')
                            ->body($summaryMsg . $errSummary)
                            ->send();
                    }
                }),
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
