<?php

namespace App\Filament\Actions;

use App\Models\MsmeBusinessData;
use App\Models\UserData;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class HeaderReadFilter implements IReadFilter
{
    public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool
    {
        return $row === 1;
    }
}

class ImportExcelAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'importExcel';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Import Excel')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('success')
            ->modalHeading('Import Bulk User Data from Excel')
            ->modalDescription('Upload an Excel file (.xlsx or .xls) and map each Excel column header to its corresponding database field before importing.')
            ->modalSubmitActionLabel('Import Data')
            ->form([
                Forms\Components\FileUpload::make('excel_file')
                    ->label('Upload Excel File (.xlsx / .xls)')
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-excel',
                        'text/csv',
                        'application/csv',
                        'text/plain',
                    ])
                    ->disk('local')
                    ->directory('temp-excel-imports')
                    ->maxSize(102400)
                    ->required()
                    ->live()
                    ->columnSpanFull()
                    ->helperText('Upload your Excel file to read headers and configure database field mapping.'),

                Forms\Components\Group::make()
                    ->schema(function (Get $get): array {
                        $filePath = $get('excel_file');
                        if (is_array($filePath)) {
                            $filePath = reset($filePath);
                        }

                        if (empty($filePath) || !is_string($filePath)) {
                            return [];
                        }

                        $fullPath = Storage::disk('local')->path($filePath);
                        if (!file_exists($fullPath)) {
                            return [];
                        }

                        try {
                            $reader = IOFactory::createReaderForFile($fullPath);
                            $reader->setReadDataOnly(true);
                            $reader->setReadFilter(new HeaderReadFilter());
                            $spreadsheet = $reader->load($fullPath);
                            $worksheet = $spreadsheet->getActiveSheet();
                            $highestColumn = $worksheet->getHighestColumn();
                            $headerRow = $worksheet->rangeToArray('A1:' . $highestColumn . '1', null, true, false, false)[0] ?? [];

                            $spreadsheet->disconnectWorksheets();
                            unset($spreadsheet, $worksheet, $reader);

                            if (empty($headerRow)) {
                                return [];
                            }

                            $mappingFields = [];
                            $dbFieldOptions = [
                                'company' => 'company (Company)',
                                'person_name' => 'person_name (Person Name)',
                                'address' => 'address (Address)',
                                'city' => 'city (City)',
                                'pincode' => 'pincode (Pincode)',
                                'state' => 'state (State)',
                                'office_no' => 'office_no (Office No.)',
                                'mobile_no' => 'mobile_no (Mobile No.)',
                                'email' => 'email (Email)',
                                'website' => 'website (Website)',
                                'details' => 'details (Details)',
                                'skip' => '-- Do Not Import / Skip --',
                            ];

                            foreach ($headerRow as $index => $colLabel) {
                                $colLabelTrimmed = trim((string) $colLabel);
                                if ($colLabelTrimmed === '') {
                                    continue;
                                }

                                $defaultMatch = self::autoMatchDbField($colLabelTrimmed);

                                $mappingFields[] = Forms\Components\Select::make("mappings.col_{$index}")
                                    ->label("Excel Column: \"{$colLabelTrimmed}\"")
                                    ->options($dbFieldOptions)
                                    ->default($defaultMatch)
                                    ->required();
                            }

                            if (!empty($mappingFields)) {
                                return [
                                    Forms\Components\Section::make('Database Field Mapping')
                                        ->description('Map each Excel column label to the corresponding original database table field name.')
                                        ->schema($mappingFields)
                                        ->columns(2),
                                ];
                            }
                        } catch (\Throwable $e) {
                            return [
                                Forms\Components\Placeholder::make('file_error')
                                    ->content('Error reading Excel file headers: ' . $e->getMessage())
                                    ->columnSpanFull(),
                            ];
                        }

                        return [];
                    })
                    ->columnSpanFull(),
            ])
            ->action(function (array $data) {
                $filePath = $data['excel_file'] ?? null;
                if (is_array($filePath)) {
                    $filePath = reset($filePath);
                }
                $mappings = $data['mappings'] ?? [];

                if (empty($filePath)) {
                    Notification::make()
                        ->danger()
                        ->title('Import Failed')
                        ->body('No Excel file was uploaded.')
                        ->send();
                    return;
                }

                $fullPath = Storage::disk('local')->path($filePath);
                if (!file_exists($fullPath)) {
                    Notification::make()
                        ->danger()
                        ->title('Import Failed')
                        ->body('Uploaded Excel file could not be found.')
                        ->send();
                    return;
                }

                // Filter out skipped fields
                $mappedDbFields = array_filter($mappings, fn($v) => $v !== 'skip');
                if (empty($mappedDbFields)) {
                    Notification::make()
                        ->warning()
                        ->title('No Mapped Fields')
                        ->body('Please map at least one Excel column to a database field before importing.')
                        ->send();
                    return;
                }

                try {
                    ini_set('memory_limit', '2048M');
                    set_time_limit(0);
                    DB::disableQueryLog();

                    // Read all rows reliably into array
                    $reader = IOFactory::createReaderForFile($fullPath);
                    $reader->setReadDataOnly(true);
                    $spreadsheet = $reader->load($fullPath);
                    $worksheet = $spreadsheet->getActiveSheet();
                    $rows = $worksheet->toArray(null, true, false, false);

                    $spreadsheet->disconnectWorksheets();
                    unset($spreadsheet, $worksheet, $reader);

                    if (count($rows) <= 1) {
                        Notification::make()
                            ->warning()
                            ->title('Empty Excel File')
                            ->body('The uploaded Excel file has no data rows to import.')
                            ->send();
                        return;
                    }

                    // Pre-fetch all existing emails into key-lookup arrays for instant O(1) duplicate checks
                    $existingEmails = [];
                    foreach (UserData::query()->whereNotNull('email')->where('email', '!=', '')->pluck('email') as $eVal) {
                        $existingEmails[strtolower(trim((string) $eVal))] = true;
                    }

                    $existingMsmeEmails = [];
                    foreach (MsmeBusinessData::query()->whereNotNull('email')->where('email', '!=', '')->pluck('email') as $eVal) {
                        $existingMsmeEmails[strtolower(trim((string) $eVal))] = true;
                    }

                    $successCount = 0;
                    $skippedCount = 0;
                    $failedCount = 0;
                    $errors = [];
                    $processedEmailsInBatch = [];

                    // Process row 1 (index 1) onwards
                    for ($rowIndex = 1; $rowIndex < count($rows); $rowIndex++) {
                        $row = $rows[$rowIndex];
                        $actualRowNumber = $rowIndex + 1;

                        // Check if row is completely empty
                        $hasContent = false;
                        foreach ($row as $cellVal) {
                            if (trim((string) $cellVal) !== '') {
                                $hasContent = true;
                                break;
                            }
                        }

                        if (!$hasContent) {
                            continue;
                        }

                        $recordData = [
                            'company' => null,
                            'person_name' => null,
                            'address' => null,
                            'add' => null,
                            'city' => null,
                            'pincode' => null,
                            'state' => null,
                            'office_no' => null,
                            'mobile_no' => null,
                            'email' => null,
                            'website' => null,
                            'details' => null,
                        ];

                        foreach ($mappings as $colKey => $dbField) {
                            if ($dbField === 'skip') {
                                continue;
                            }

                            $colIdx = (int) str_replace('col_', '', $colKey);
                            $val = isset($row[$colIdx]) ? trim((string) $row[$colIdx]) : null;

                            if ($val !== '' && $val !== null) {
                                $recordData[$dbField] = $val;
                                if ($dbField === 'address') {
                                    $recordData['add'] = $val;
                                }
                            }
                        }

                        // Validate non-empty mapped values
                        $nonEmptyValues = array_filter($recordData, fn($v) => !is_null($v) && $v !== '');
                        if (empty($nonEmptyValues)) {
                            $failedCount++;
                            $errors[] = "Row #" . $actualRowNumber . ": No valid mapped data found.";
                            continue;
                        }

                        // O(1) Instant duplicate email check
                        $email = !empty($recordData['email']) ? strtolower(trim((string) $recordData['email'])) : null;
                        if (!empty($email)) {
                            if (
                                isset($processedEmailsInBatch[$email]) ||
                                isset($existingEmails[$email]) ||
                                isset($existingMsmeEmails[$email])
                            ) {
                                $skippedCount++;
                                continue;
                            }
                            $processedEmailsInBatch[$email] = true;
                        }

                        try {
                            // Insert into msme_business_local table (MsmeBusinessData model)
                            $msmeData = MsmeBusinessData::create([
                                'company' => $recordData['company'],
                                'person_name' => $recordData['person_name'],
                                'add' => $recordData['address'] ?? $recordData['add'],
                                'address' => $recordData['address'] ?? $recordData['add'],
                                'city' => $recordData['city'],
                                'pincode' => $recordData['pincode'],
                                'state' => $recordData['state'],
                                'office_no' => $recordData['office_no'],
                                'mobile_no' => $recordData['mobile_no'],
                                'email' => $recordData['email'],
                                'website' => $recordData['website'],
                                'details' => $recordData['details'],
                            ]);

                            // Insert/Sync into user_data table (UserData model)
                            UserData::create([
                                'msme_business_data_id' => $msmeData->id,
                                'is_bulk_user' => true,
                                'company' => $recordData['company'],
                                'person_name' => $recordData['person_name'],
                                'organization_name' => $recordData['company'] ?? $recordData['person_name'],
                                'email' => $recordData['email'],
                                'contact_no' => $recordData['mobile_no'] ?? $recordData['office_no'] ?? '0000000000',
                                'address' => $recordData['address'] ?? $recordData['add'],
                                'city' => $recordData['city'] ?? '',
                                'state' => $recordData['state'] ?? '',
                                'pincode' => !empty($recordData['pincode']) ? (int)$recordData['pincode'] : 0,
                                'office_no' => $recordData['office_no'],
                                'mobile_no' => $recordData['mobile_no'],
                                'website' => $recordData['website'],
                                'details' => $recordData['details'],
                                'business_type' => 0,
                                'type_organization' => 'MSME',
                                'msme_type' => 'Micro',
                                'business_category' => 1,
                                'specialization' => 'General',
                                'experience' => 1,
                                'document_no' => 'N/A',
                                'document_file' => [],
                                'state_city' => $recordData['state'] ?? 'All',
                                'business_size' => ['micro'],
                                'approve' => 1,
                                'want_to' => 'Connect',
                            ]);

                            $successCount++;
                        } catch (\Throwable $e) {
                            $failedCount++;
                            $errors[] = "Row #" . $actualRowNumber . ": " . $e->getMessage();
                        }
                    }

                    // Delete temp upload file
                    Storage::disk('local')->delete($filePath);

                    // Show notification summary
                    $summaryMsg = "Imported {$successCount} records successfully.";
                    if ($skippedCount > 0) {
                        $summaryMsg .= " {$skippedCount} records skipped (email already exists).";
                    }

                    if ($failedCount === 0 && $skippedCount === 0) {
                        Notification::make()
                            ->success()
                            ->title('Excel Import Successful')
                            ->body($summaryMsg)
                            ->send();
                    } else {
                        if ($failedCount > 0) {
                            $summaryMsg .= " {$failedCount} records failed.";
                        }

                        $errSummary = '';
                        if (!empty($errors)) {
                            $errSummary = "\n\nErrors:\n" . implode("\n", array_slice($errors, 0, 5));
                            if (count($errors) > 5) {
                                $errSummary .= "\n...and " . (count($errors) - 5) . " more errors.";
                            }
                        }

                        Notification::make()
                            ->warning()
                            ->title('Excel Import Completed')
                            ->body($summaryMsg . $errSummary)
                            ->send();
                    }
                } catch (\Throwable $e) {
                    Notification::make()
                        ->danger()
                        ->title('Excel Import Error')
                        ->body('Failed to process Excel file: ' . $e->getMessage())
                        ->send();
                }
            });
    }

    protected static function autoMatchDbField(string $label): string
    {
        $clean = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $label));

        return match (true) {
            str_contains($clean, 'company') || str_contains($clean, 'firm') || str_contains($clean, 'org') => 'company',
            str_contains($clean, 'person') || str_contains($clean, 'contactperson') || (str_contains($clean, 'name') && !str_contains($clean, 'company')) => 'person_name',
            str_contains($clean, 'add') || str_contains($clean, 'street') || str_contains($clean, 'location') => 'address',
            str_contains($clean, 'city') || str_contains($clean, 'town') => 'city',
            str_contains($clean, 'pin') || str_contains($clean, 'zip') || str_contains($clean, 'postal') => 'pincode',
            str_contains($clean, 'state') || str_contains($clean, 'region') || str_contains($clean, 'province') => 'state',
            str_contains($clean, 'office') || str_contains($clean, 'tel') || str_contains($clean, 'landline') => 'office_no',
            str_contains($clean, 'mobile') || str_contains($clean, 'phone') || str_contains($clean, 'cell') => 'mobile_no',
            str_contains($clean, 'email') || str_contains($clean, 'mail') => 'email',
            str_contains($clean, 'web') || str_contains($clean, 'site') || str_contains($clean, 'url') => 'website',
            str_contains($clean, 'detail') || str_contains($clean, 'remark') || str_contains($clean, 'note') || str_contains($clean, 'desc') => 'details',
            default => 'skip',
        };
    }
}
