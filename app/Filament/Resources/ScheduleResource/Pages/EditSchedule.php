<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use App\Filament\Resources\ScheduleResource;
use App\Notifications\ScheduleUpdated;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;


class EditSchedule extends EditRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // Obtener el horario que está siendo editado
        $schedule = $this->getRecord();
        
        // Enviar notificación a la base de datos
        Auth::user()->notify(new ScheduleUpdated($schedule));
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Horario actualizado')
            ->body('El horario ha sido actualizado correctamente')
            ->sendToDatabase(Auth::user());
    }
}
