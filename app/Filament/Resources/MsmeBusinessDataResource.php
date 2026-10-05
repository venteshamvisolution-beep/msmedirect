<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MsmeBusinessDataResource\Pages;
use App\Models\MsmeBusinessData;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MsmeBusinessDataResource extends Resource
{
    protected static ?string $model = MsmeBusinessData::class;

    protected static ?string $navigationGroup = 'Business Data';

    protected static ?string $navigationLabel = 'Business Data';

    protected static ?string $modelLabel = 'Business Data';

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('s_no')
                    ->label('S.No'),
                Forms\Components\TextInput::make('company')
                    ->maxLength(255),
                Forms\Components\TextInput::make('person_name')
                    ->label('Contact Person')
                    ->maxLength(255),
                Forms\Components\TextInput::make('mobile_no')
                    ->label('Mobile No')
                    ->maxLength(100),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('office_no')
                    ->label('Office No')
                    ->maxLength(100),
                Forms\Components\TextInput::make('city')
                    ->maxLength(255),
                Forms\Components\TextInput::make('pincode')
                    ->maxLength(100),
                Forms\Components\TextInput::make('state')
                    ->maxLength(255),
                Forms\Components\TextInput::make('website')
                    ->maxLength(255),
                Forms\Components\Textarea::make('add')
                    ->label('Address')
                    ->rows(3)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('details')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['latestCampaignItem', 'userData']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->defaultPaginationPageOption(10)
            ->paginationPageOptions([10, 25, 50, 100])
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('s_no')
                    ->label('S.No')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('company')
                    ->label('Company Name')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('person_name')
                    ->label('Person Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mobile_no')
                    ->label('Mobile No')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('mail_status')
                    ->label('Mail Status')
                    ->getStateUsing(function (MsmeBusinessData $record): string {
                        if ($record->userData || (!empty($record->email) && \App\Models\UserData::where('email', $record->email)->exists())) {
                            return 'Registered';
                        }

                        $item = $record->latestCampaignItem;
                        if (!$item) {
                            return 'Not Sent';
                        }

                        if ($item->status === 'failed' || in_array($item->delivery_status, ['failed', 'bounced', 'complained'])) {
                            return 'Failed';
                        }

                        if ($item->delivery_status === 'delivered') {
                            return 'Delivered';
                        }

                        if ($item->status === 'sent' || $item->delivery_status === 'sent') {
                            return 'Sent';
                        }

                        return ucfirst($item->status ?? 'Pending');
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Registered' => 'success',
                        'Failed' => 'danger',
                        'Sent' => 'info',
                        'Delivered' => 'success',
                        'Pending', 'Sending' => 'warning',
                        default => 'gray',
                    })
                    ->tooltip(function (MsmeBusinessData $record): ?string {
                        $item = $record->latestCampaignItem;
                        if ($item && ($item->status === 'failed' || in_array($item->delivery_status, ['failed', 'bounced', 'complained']))) {
                            return $item->error_message ?: 'Mail sending failed';
                        }
                        return null;
                    }),
                Tables\Columns\TextColumn::make('city')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('state')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pincode')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('office_no')
                    ->label('Office No')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('website')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('add')
                    ->label('Address')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('details')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('mail_status')
                    ->label('Mail Status')
                    ->options([
                        'registered' => 'Registered',
                        'failed' => 'Failed',
                        'sent' => 'Sent / Delivered',
                        'pending' => 'Pending',
                        'not_sent' => 'Not Sent',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['value'])) {
                            return $query;
                        }

                        return match ($data['value']) {
                            'registered' => $query->where(function (Builder $q) {
                                $q->whereHas('userData')
                                    ->orWhereIn('email', \App\Models\UserData::whereNotNull('email')->where('email', '!=', '')->pluck('email'));
                            }),
                            'failed' => $query->whereHas('campaignItems', function (Builder $q) {
                                $q->where('status', 'failed')
                                    ->orWhereIn('delivery_status', ['failed', 'bounced', 'complained']);
                            }),
                            'sent' => $query->whereHas('campaignItems', function (Builder $q) {
                                $q->whereIn('status', ['sent', 'delivered'])
                                    ->orWhereIn('delivery_status', ['sent', 'delivered']);
                            }),
                            'pending' => $query->whereHas('campaignItems', function (Builder $q) {
                                $q->whereIn('status', ['pending', 'sending']);
                            }),
                            'not_sent' => $query->whereDoesntHave('campaignItems'),
                            default => $query,
                        };
                    }),
                Tables\Filters\SelectFilter::make('state')
                    ->options(fn() => MsmeBusinessData::query()
                        ->whereNotNull('state')
                        ->where('state', '!=', '')
                        ->distinct()
                        ->pluck('state', 'state')
                        ->toArray()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListMsmeBusinessData::route('/'),
            'create' => Pages\CreateMsmeBusinessData::route('/create'),
            'edit' => Pages\EditMsmeBusinessData::route('/{record}/edit'),
        ];
    }
}
