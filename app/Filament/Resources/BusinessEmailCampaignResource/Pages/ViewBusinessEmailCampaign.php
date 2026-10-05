<?php

namespace App\Filament\Resources\BusinessEmailCampaignResource\Pages;

use App\Filament\Resources\BusinessEmailCampaignResource;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewBusinessEmailCampaign extends ViewRecord
{
    protected static string $resource = BusinessEmailCampaignResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Campaign Details')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Campaign Name'),
                        TextEntry::make('range')
                            ->label('ID Range')
                            ->getStateUsing(fn($record) => $record->start_id && $record->end_id ? "IDs {$record->start_id} to {$record->end_id}" : 'All'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'pending' => 'warning',
                                'processing' => 'primary',
                                'completed' => 'success',
                                'failed' => 'danger',
                                default => 'secondary',
                            }),
                        TextEntry::make('started_at')
                            ->dateTime(),
                        TextEntry::make('completed_at')
                            ->dateTime(),
                    ])->columns(3),

                Section::make('Campaign Statistics (Batch Breakdown)')
                    ->schema([
                        TextEntry::make('total_count')
                            ->label('Total Records in Range')
                            ->weight('bold'),
                        TextEntry::make('skipped_count')
                            ->label('Skipped (Already Sent / Registered)')
                            ->color('warning')
                            ->weight('bold'),
                        TextEntry::make('queued_count')
                            ->label('Eligible Queued'),
                        TextEntry::make('sent_count')
                            ->label('Successfully Sent')
                            ->color('success')
                            ->weight('bold'),
                        TextEntry::make('failed_count')
                            ->label('Failed')
                            ->color('danger')
                            ->weight('bold'),
                        TextEntry::make('pending_count')
                            ->label('Pending Queue'),
                        TextEntry::make('progress_percent')
                            ->label('Completion %')
                            ->formatStateUsing(fn($state) => $state . '%'),
                    ])->columns(3),

                Section::make('Error Log & Failure Trace')
                    ->schema([
                        TextEntry::make('error_log')
                            ->label('')
                            ->markdown()
                            ->placeholder('No errors recorded.'),
                    ])
                    ->collapsible()
                    ->visible(fn($record) => !empty($record->error_log)),
            ]);
    }
}
