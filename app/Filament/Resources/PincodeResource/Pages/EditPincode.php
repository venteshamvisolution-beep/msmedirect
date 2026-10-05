<?php

namespace App\Filament\Resources\PincodeResource\Pages;

use App\Filament\Resources\PincodeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPincode extends EditRecord
{
    protected static string $resource = PincodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
