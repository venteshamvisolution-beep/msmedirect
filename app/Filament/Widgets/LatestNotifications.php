<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use App\Models\UserData;
use Filament\Tables\Actions\Action;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestNotifications extends BaseWidget
{
    // protected static ?string $title = 'User Data';
    protected int | string | array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        
        
        return $table
            ->query(
                UserData::query()->whereNull("approve")
            )
            ->columns([
                 Tables\Columns\TextColumn::make('organization_name')
                    ->searchable(),
                    Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                   
                // ...
            ])->actions([
                Action::make('edit')
                    ->url(fn (UserData $record): string => route('filament.admin.resources.user-datas.edit', $record))
                    ->openUrlInNewTab()
            ]);
    }
}
