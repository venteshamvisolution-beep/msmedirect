<?php

namespace App\Filament\Resources\ConnectionsResource\Pages;

use App\Filament\Resources\ConnectionsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Models\Connections;

class ViewConnections extends ViewRecord
{
    protected static string $resource = ConnectionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
        ];
    }
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = Connections::with('userTableDataFrom')->find($data['id']);
        $data['applicant_name'] = $record->userTableDataFrom->name;
        $record = Connections::with('userTableDataConn')->find($data['id']);
        $data['recipent_name'] = $record->userTableDataConn->name;
        
        
        $record = Connections::with('businessTypeConn')->find($data['id']);
        $data['recipent_business'] = $record->businessTypeConn->business_name;
        $record = Connections::with('businessTypeFrom')->find($data['id']);
        $data['applicant_business'] = $record->businessTypeFrom->business_name;
        
        $status = config('app.status');
        $data['status'] = $status[$data['status']];
        return $data;
    }
}
