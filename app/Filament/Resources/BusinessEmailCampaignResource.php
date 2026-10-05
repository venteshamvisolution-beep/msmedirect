<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BusinessEmailCampaignResource\Pages;
use App\Models\BusinessEmailCampaign;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BusinessEmailCampaignResource extends Resource
{
    protected static ?string $model = BusinessEmailCampaign::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationGroup = 'Business Data';

    protected static ?string $navigationLabel = 'Email Campaigns';

    protected static ?string $modelLabel = 'Email Campaign';

    protected static ?string $pluralModelLabel = 'Email Campaigns';

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('subject')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('content')
                    ->rows(4)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('status')
                    ->disabled(),
                Forms\Components\TextInput::make('total_count')
                    ->label('Total Records')
                    ->disabled(),
                Forms\Components\TextInput::make('skipped_count')
                    ->label('Skipped (Sent/Registered)')
                    ->disabled(),
                Forms\Components\TextInput::make('queued_count')
                    ->label('Queued Count')
                    ->disabled(),
                Forms\Components\TextInput::make('sent_count')
                    ->label('Sent Count')
                    ->disabled(),
                Forms\Components\TextInput::make('delivered_count')
                    ->label('Delivered Count')
                    ->disabled(),
                Forms\Components\TextInput::make('failed_count')
                    ->label('Failed Count')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),
                Tables\Columns\TextColumn::make('range')
                    ->label('ID Range')
                    ->getStateUsing(fn(BusinessEmailCampaign $record) => $record->start_id && $record->end_id ? "{$record->start_id} - {$record->end_id}" : 'All'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'primary',
                        'completed' => 'success',
                        'failed' => 'danger',
                        default => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('total_count')
                    ->label('Total')
                    ->sortable(),
                Tables\Columns\TextColumn::make('skipped_count')
                    ->label('Skipped')
                    ->sortable()
                    ->color('warning')
                    ->tooltip('Skipped because email was already sent or registered in user_data'),
                Tables\Columns\TextColumn::make('queued_count')
                    ->label('Queued')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sent_count')
                    ->label('Sent')
                    ->sortable()
                    ->color('info'),
                Tables\Columns\TextColumn::make('delivered_count')
                    ->label('Delivered')
                    ->sortable()
                    ->color('success'),
                Tables\Columns\TextColumn::make('failed_count')
                    ->label('Failed')
                    ->sortable()
                    ->color('danger'),
                Tables\Columns\TextColumn::make('progress')
                    ->label('Progress')
                    ->getStateUsing(fn(BusinessEmailCampaign $record) => $record->progress_percent . '%'),
                // Tables\Columns\TextColumn::make('started_at')
                //     ->dateTime()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('completed_at')
                //     ->dateTime()
                //     ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\Action::make('retryBatch')
                        ->label('Retry Batch')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Retry Batch')
                        ->modalDescription('Re-evaluate duplicate checks (skipping sent/registered emails) and retry queueing remaining emails for this batch.')
                        ->action(function (BusinessEmailCampaign $record) {
                            $record->update([
                                'status' => 'pending',
                                'error_log' => null,
                            ]);

                            \App\Jobs\StartBusinessEmailCampaign::dispatch($record->id);

                            \Filament\Notifications\Notification::make()
                                ->success()
                                ->title('Batch Retried')
                                ->body("Batch campaign #{$record->id} ({$record->name}) has been queued for retry.")
                                ->send();
                        })
                        ->visible(fn(BusinessEmailCampaign $record) => in_array($record->status, ['failed', 'completed', 'cancelled'])),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBusinessEmailCampaigns::route('/'),
            'view' => Pages\ViewBusinessEmailCampaign::route('/{record}'),
        ];
    }
}
