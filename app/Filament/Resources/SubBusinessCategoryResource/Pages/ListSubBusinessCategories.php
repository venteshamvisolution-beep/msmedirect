<?php

namespace App\Filament\Resources\SubBusinessCategoryResource\Pages;

use App\Filament\Resources\SubBusinessCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubBusinessCategories extends ListRecords
{
    protected static string $resource = SubBusinessCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
