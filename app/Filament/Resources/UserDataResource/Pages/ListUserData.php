<?php

namespace App\Filament\Resources\UserDataResource\Pages;

use App\Filament\Resources\UserDataResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserData extends ListRecords
{
    protected static string $resource = UserDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
