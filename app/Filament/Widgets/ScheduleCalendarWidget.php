<?php

namespace App\Filament\Widgets;

use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Models\Schedule;
use Filament\Forms;
use Illuminate\Support\Facades\Auth;
use Saade\FilamentFullCalendar\Data\EventData;
use Illuminate\Database\Eloquent\Model;

class ScheduleCalendarWidget extends FullCalendarWidget
{
    public Model|string|null $model = Schedule::class;

    /**
     * Recupera los horarios del profesional autenticado para mostrar en el calendario.
     */
    public function fetchEvents(array $fetchInfo): array
    {
        return Schedule::query()
            ->where('user_id', Auth::id())
            ->whereBetween('date', [$fetchInfo['start'], $fetchInfo['end']])
            ->get()
            ->map(function (Schedule $schedule) {
                return EventData::make()
                    ->id($schedule->id)
                    ->title('Horario Disponible')
                    ->start($schedule->date->setTimeFromTimeString($schedule->start_time))
                    ->end($schedule->date->setTimeFromTimeString($schedule->end_time))
                    ->backgroundColor($schedule->is_available ? '#22c55e' : '#ef4444')
                    ->toArray();
            })
            ->toArray();
    }

    /**
     * Define el formulario para crear/editar horarios desde el calendario.
     */
    public function getFormSchema(): array
    {
        return [
            Forms\Components\Hidden::make('user_id')
                ->default(Auth::id()),
            Forms\Components\DatePicker::make('date')
                ->label('Fecha')
                ->required()
                ->minDate(now())
                ->reactive(),
            Forms\Components\TimePicker::make('start_time')
                ->label('Hora de Inicio')
                ->required()
                ->seconds(false)
                ->reactive(),
            Forms\Components\TimePicker::make('end_time')
                ->label('Hora de Fin')
                ->required()
                ->seconds(false)
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set, $get) {
                    $startTime = $get('start_time');
                    if ($startTime && $state && $state <= $startTime) {
                        $set('end_time', null);
                    }
                }),
            Forms\Components\Toggle::make('is_available')
                ->label('Disponible')
                ->default(true),
        ];
    }

    /**
     * Configuración adicional del calendario.
     */
    public function config(): array
    {
        return [
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,timeGridDay',
            ],
            'initialView' => 'timeGridWeek', // Vista inicial en formato semanal con horas
            'slotDuration' => '00:30:00', // Intervalos de 30 minutos
            'businessHours' => [
                'daysOfWeek' => [1, 2, 3, 4, 5], // Lunes a viernes
                'startTime' => '08:00',
                'endTime' => '18:00',
            ],
        ];
    }

    /**
     * Acciones disponibles en el encabezado.
     */
    protected function headerActions(): array
    {
        return [
            \Saade\FilamentFullCalendar\Actions\CreateAction::make(),
        ];
    }

    /**
     * Acciones disponibles en el modal al interactuar con un evento.
     */
    protected function modalActions(): array
    {
        return [
            \Saade\FilamentFullCalendar\Actions\EditAction::make(),
            \Saade\FilamentFullCalendar\Actions\DeleteAction::make(),
        ];
    }
}
