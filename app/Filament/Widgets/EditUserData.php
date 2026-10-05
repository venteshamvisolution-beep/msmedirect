<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\NewUserData;
use Filament\Tables\Actions\Action;

class EditUserData extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table
            ->query(
                NewUserData::query()
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
