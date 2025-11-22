<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use App\Filament\Resources\ScheduleResource;
use App\Models\Schedule;
use App\Notifications\ScheduleCreated;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CreateSchedule extends CreateRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function beforeCreate(): void
    {
        $data = $this->form->getState();

        // Validar horarios básicos antes de crear
        if (!$this->validateScheduleBasics($data)) {
            $this->halt();
        }

        // Validar horarios superpuestos antes de crear
        if (!$this->validateOverlappingSchedules($data)) {
            $this->halt();
        }
    }

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

    /**
     * Valida las reglas básicas del horario
     */
    protected function validateScheduleBasics(array $data): bool
    {
        $startTime = $data['start_time'] ?? null;
        $endTime = $data['end_time'] ?? null;
        $date = $data['date'] ?? null;

        if (!$startTime || !$endTime) {
            return true;
        }

        $start = strtotime($startTime);
        $end = strtotime($endTime);

        // Validar que la hora de fin sea posterior a la de inicio
        if ($end <= $start) {
            Notification::make()
                ->title('Error en el horario')
                ->body('La hora de fin debe ser posterior a la hora de inicio. No se pueden crear horarios con la misma hora de inicio y fin.')
                ->danger()
                ->duration(5000)
                ->send();
            return false;
        }

        // Validar que sea día laboral (lunes a viernes)
        if ($date) {
            $dayOfWeek = date('N', strtotime($date));
            if ($dayOfWeek > 5) {
                Notification::make()
                    ->title('Fecha no válida')
                    ->body('Solo se pueden crear horarios de lunes a viernes.')
                    ->danger()
                    ->duration(5000)
                    ->send();
                return false;
            }
        }

        return true;
    }

    /**
     * Valida si existen horarios superpuestos
     */
    protected function validateOverlappingSchedules(array $data): bool
    {
        $date = $data['date'] ?? null;
        $startTime = $data['start_time'] ?? null;
        $endTime = $data['end_time'] ?? null;
        $userId = $data['user_id'] ?? null;

        if (!$date || !$startTime || !$endTime || !$userId) {
            return true;
        }

        // Convertir los tiempos a objetos DateTime para comparar
        $start = strtotime($startTime);
        $end = strtotime($endTime);

        // Buscar horarios existentes para la misma fecha y usuario
        $existingSchedules = Schedule::where('date', $date)
            ->where('user_id', $userId)
            ->get();

        // Verificar si hay alguna superposición de horarios
        foreach ($existingSchedules as $schedule) {
            $existingStart = strtotime($schedule->start_time);
            $existingEnd = strtotime($schedule->end_time);

            // Comprobar si hay superposición
            if (
                ($start >= $existingStart && $start < $existingEnd) ||
                ($end > $existingStart && $end <= $existingEnd) ||
                ($start <= $existingStart && $end >= $existingEnd)
            ) {
                // Mostrar notificación de error
                Notification::make()
                    ->title('Horario superpuesto')
                    ->body('Ya existe un horario para este profesional en esta fecha que se superpone con el horario seleccionado (' .
                        date('h:i A', $existingStart) . ' - ' . date('h:i A', $existingEnd) . ').')
                    ->danger()
                    ->duration(7000)
                    ->send();
                return false;
            }
        }

        return true;
    }
}
