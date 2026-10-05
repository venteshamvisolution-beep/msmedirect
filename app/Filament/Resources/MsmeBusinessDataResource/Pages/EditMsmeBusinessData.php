<?php

namespace App\Filament\Resources\MsmeBusinessDataResource\Pages;

use App\Filament\Resources\MsmeBusinessDataResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMsmeBusinessData extends EditRecord
{
    protected static string $resource = MsmeBusinessDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
