<?php

namespace App\Filament\Resources\SubBusinessCategoryResource\Pages;

use App\Filament\Resources\SubBusinessCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubBusinessCategory extends EditRecord
{
    protected static string $resource = SubBusinessCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
