<?php

namespace App\Filament\Resources\NewUserDataResource\Pages;

use App\Filament\Resources\NewUserDataResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNewUserData extends ListRecords
{
    protected static string $resource = NewUserDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
