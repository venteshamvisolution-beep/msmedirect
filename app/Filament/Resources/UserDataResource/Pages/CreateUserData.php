<?php

namespace App\Filament\Resources\UserDataResource\Pages;

use App\Filament\Resources\UserDataResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUserData extends CreateRecord
{
    protected static string $resource = UserDataResource::class;
}
