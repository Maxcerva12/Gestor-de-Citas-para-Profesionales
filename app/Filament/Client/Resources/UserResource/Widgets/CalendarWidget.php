<?php

namespace App\Filament\Client\Resources\UserResource\Widgets;

use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Models\Schedule;
use App\Models\Appointment;
use Carbon\Carbon;
use Filament\Forms;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

class CalendarWidget extends FullCalendarWidget
{
    public Model|string|int|null $record = null;

    // Configuración del calendario
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
            'droppable' => false,
            'select' => [
                'enabled' => false,
            ],
            'dayMaxEvents' => true,
            'locale' => 'es',
            'timeZone' => config('app.timezone'),
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

    // Devuelve los eventos (slots disponibles)
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

            $events[] = [
                'id' => $slot->id,
                'title' => 'Disponible: ' . $startDateTime->format('H:i') . ' - ' . $endDateTime->format('H:i'),
                'start' => $startDateTime->format('Y-m-d H:i:s'),
                'end' => $endDateTime->format('Y-m-d H:i:s'),
                'backgroundColor' => '#22c55e', // Verde para disponible
                'borderColor' => '#16a34a',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'schedule_id' => $slot->id,
                    'date' => $date,
                    'start_time' => $slot->start_time,
                    'end_time' => $slot->end_time,
                ],
            ];
        }

        return $events;
    }

    // Acción al hacer click en un evento del calendario
    public function onEventClick($event): void
    {
        Log::info('Evento click recibido con ID: ' . $event['id']);

        // Dispara la acción nativa de "crear"
        $this->mountAction('create', [
            'schedule_id' => $event['id'],
            'date' => $event['extendedProps']['date'],
            'start_time' => $event['extendedProps']['start_time'],
            'end_time' => $event['extendedProps']['end_time'],
        ]);
    }

    // Propiedades del formulario de creación de cita
    // Propiedades del formulario de creación de cita
    public function getFormSchema(): array
    {
        return [
            Forms\Components\Hidden::make('schedule_id')
                ->required(),
            Forms\Components\Section::make('Detalles de la cita')
                ->description('Información sobre el horario seleccionado')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\DatePicker::make('date')
                                ->label('Fecha')
                                ->disabled()
                                ->required(),
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\TextInput::make('start_time')
                                        ->label('Hora de inicio')
                                        ->disabled()
                                        ->required(),
                                    Forms\Components\TextInput::make('end_time')
                                        ->label('Hora de fin')
                                        ->disabled()
                                        ->required(),
                                ]),
                        ]),
                    Forms\Components\Textarea::make('notes')
                        ->label('Motivo o notas de la cita')
                        ->placeholder('Describe brevemente el motivo de tu cita...')
                        ->maxLength(500)
                        ->columnSpanFull(),
                ]),
        ];
    }

    // Qué pasa al guardar la cita
    public function create(array $data): void
    {
        Log::info('Creando cita con datos: ' . json_encode($data));

        $schedule = Schedule::findOrFail($data['schedule_id']);

        // Crear la cita
        $appointment = Appointment::create([
            'professional_id' => $schedule->user_id,
            'client_id' => auth()->user()->id,
            'date' => $data['date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'notes' => $data['notes'] ?? null,
            'status' => 'pending',
        ]);

        // Notificar éxito
        $this->notify('success', 'Cita reservada correctamente. Continúa con el pago.');

        // Redirigir a la página de pago
        $this->redirect(route('client.payment.process', [
            'appointment' => $appointment->id,
        ]));
    }

    public function getActions(): array
    {
        return [
            \Filament\Actions\Action::make('create')
                ->label('Reservar cita')
                ->form($this->getFormSchema())
                ->action(fn(array $data) => $this->create($data))
                ->modalHeading('Reservar cita')
                ->modalSubmitActionLabel('Confirmar reserva')
        ];
    }

    protected static bool $isLazy = false;
}