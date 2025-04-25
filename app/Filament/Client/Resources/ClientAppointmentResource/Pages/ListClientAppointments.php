<?php

namespace App\Filament\Client\Resources\ClientAppointmentResource\Pages;

use App\Filament\Client\Resources\ClientAppointmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClientAppointments extends ListRecords
{
    protected static string $resource = ClientAppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
