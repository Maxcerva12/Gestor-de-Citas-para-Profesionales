<?php

namespace App\Filament\Client\Resources\UserResource\Widgets;

use App\Models\Schedule;
use App\Models\Appointment;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Saade\FilamentFullCalendar\Data\EventData;
use Illuminate\Database\Eloquent\Model;
use Filament\Actions\Action;

class CalendarWidget extends FullCalendarWidget
{
    // Cambiando la definición de $record para que sea compatible con la clase padre
    public Model|string|int|null $record = null;

    // El modelo utilizado para los eventos
    public Model|string|null $model = Schedule::class;

    // Hacemos que el widget pueda ser renderizado automáticamente
    protected static bool $isLazy = false;

    /**
     * Recupera los eventos para el calendario
     */
    public function fetchEvents(array $fetchInfo): array
    {
        // Verificamos que el record sea una instancia de User
        if (!$this->record || !$this->record instanceof \App\Models\User) {
            Log::warning('Record no es una instancia de User o está vacío');
            return [];
        }

        Log::info('Generando eventos para profesional ID: ' . $this->record->id);

        // Obtenemos todos los horarios disponibles del profesional
        $availableSlots = Schedule::where('user_id', $this->record->id)
            ->where('is_available', true)
            ->whereBetween('date', [$fetchInfo['start'], $fetchInfo['end']])
            ->get();

        Log::info('Horarios encontrados: ' . $availableSlots->count());

        $events = [];

        foreach ($availableSlots as $slot) {
            // Formatear la fecha y hora para el calendario
            $date = Carbon::parse($slot->date)->format('Y-m-d');

            // Convertir hora a formato adecuado
            $startDateTime = Carbon::parse($date . ' ' . $slot->start_time);
            $endDateTime = Carbon::parse($date . ' ' . $slot->end_time);

            // Crear un evento para el calendario usando EventData
            $events[] = EventData::make()
                ->id($slot->id)
                ->title('Disponible: ' . $startDateTime->format('H:i') . ' - ' . $endDateTime->format('H:i'))
                ->start($startDateTime)
                ->end($endDateTime)
                ->backgroundColor('#22c55e') // Verde para disponible
                ->borderColor('#16a34a')
                ->textColor('#ffffff')
                ->extendedProps([
                    'schedule_id' => $slot->id,
                    'status' => 'available',
                    'date' => $date,
                    'start_time' => $slot->start_time,
                    'end_time' => $slot->end_time,
                ])
                ->toArray();
        }

        return $events;
    }

    /**
     * Configuración del calendario
     */
    public function config(): array
    {
        return [
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,timeGridDay',
            ],
            'initialView' => 'timeGridWeek',
            'slotMinTime' => '07:00:00',
            'slotMaxTime' => '20:00:00',
            'slotDuration' => '00:30:00',
            'businessHours' => [
                'daysOfWeek' => [1, 2, 3, 4, 5], // Lunes a viernes
                'startTime' => '08:00',
                'endTime' => '18:00',
            ],
            'nowIndicator' => true,
            'selectable' => false,
            'editable' => false,
            // Agregar esta configuración para deshabilitar creación por arrastrar
            'droppable' => false,
            // Asegurarnos que no se pueden crear eventos nuevos
            'select' => [
                'enabled' => false,
            ],
            'dayMaxEvents' => true,
            'locale' => 'es',
            'timeZone' => config('app.timezone'),
            'eventClick' => [
                'enabled' => true,
                'function' => "
                    function(info) {
                        info.jsEvent.preventDefault();
                        info.jsEvent.stopPropagation();
                        console.log('Emitiendo book-appointment con datos:', {
                            scheduleId: info.event.extendedProps.schedule_id,
                            date: info.event.extendedProps.date,
                            startTime: info.event.extendedProps.start_time,
                            endTime: info.event.extendedProps.end_time
                        });
                        Livewire.dispatch('book-appointment', {
                            scheduleId: info.event.extendedProps.schedule_id,
                            date: info.event.extendedProps.date,
                            startTime: info.event.extendedProps.start_time,
                            endTime: info.event.extendedProps.end_time
                        });
                    }
                ",
            ],
            'eventDidMount' => [
                'enabled' => true,
                'function' => "
                    function(info) {
                        // Añadir tooltip con información del horario
                        tippy(info.el, {
                            content: 'Haz clic para reservar este horario: ' + info.event.title,
                            placement: 'top',
                            arrow: true,
                            theme: 'light',
                        });
                        
                        // Añadir cursor pointer para indicar que es clickeable
                        info.el.style.cursor = 'pointer';
                    }
                ",
            ],
            'buttonText' => [
                'today' => 'Hoy',
                'month' => 'Mes',
                'week' => 'Semana',
                'day' => 'Día',
            ],
            'views' => [
                'timeGridWeek' => [
                    'titleFormat' => [
                        'year' => 'numeric',
                        'month' => 'long',
                        'day' => '2-digit',
                    ],
                ],
                'timeGridDay' => [
                    'titleFormat' => [
                        'year' => 'numeric',
                        'month' => 'long',
                        'day' => '2-digit',
                    ],
                ],
            ],
        ];
    }

    /**
     * Configuración de los eventos
     */

    protected function headerActions(): array
    {
        return [];
    }

    public function modalActions(): array
    {
        return [];
    }

    protected function viewAction(): Action
    {
        return Action::make('view')->hidden();
    }
}