<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use App\Filament\Resources\ScheduleResource;
use App\Notifications\ScheduleCreated;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class CreateSchedule extends CreateRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function afterCreate(): void
    {
        // Obtener el horario recién creado
        $schedule = $this->getRecord();
        
        // Enviar notificación a la base de datos
        Auth::user()->notify(new ScheduleCreated($schedule));
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Horario creado')
            ->body('El horario ha sido creado correctamente')
            ->sendToDatabase(Auth::user());
    }
}
