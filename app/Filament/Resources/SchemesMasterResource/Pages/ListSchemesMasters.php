<?php

namespace App\Filament\Resources\SchemesMasterResource\Pages;

use App\Filament\Resources\SchemesMasterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSchemesMasters extends ListRecords
{
    protected static string $resource = SchemesMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
