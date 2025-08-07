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
    // El profesional (User) relacionado
    public Model|string|int|null $record = null;

    // ¡El modelo correcto debe ser Appointment!
    public Model|string|null $model = Appointment::class;

    public $selectedScheduleId = null;
    public $selectedDate = null;
    public $selectedStartTime = null;
    public $selectedEndTime = null;
    public $selectedStartTimeFormatted = null;
    public $selectedEndTimeFormatted = null;
    public $appointmentNotes = null;

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
                'daysOfWeek' => [1, 2, 3, 4, 5],
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

    public function fetchEvents(array $fetchInfo): array
    {
        if (!$this->record || !$this->record instanceof \App\Models\User) {
            Log::warning('Record no es una instancia de User o está vacío');
            return [];
        }

        Log::info('Generando eventos para profesional ID: ' . $this->record->id);

        $availableSlots = Schedule::where('user_id', $this->record->id)
            ->where('is_available', true)
            ->whereBetween('date', [$fetchInfo['start'], $fetchInfo['end']])
            ->whereRaw("STRFTIME('%Y-%m-%d', date) || ' ' || CASE 
            WHEN start_time LIKE '%PM' THEN 
                CASE 
                    WHEN CAST(SUBSTR(start_time, 1, 2) AS INTEGER) = 12 THEN '12'
                    ELSE CAST(CAST(SUBSTR(start_time, 1, 2) AS INTEGER) + 12 AS TEXT)
                END
            WHEN start_time LIKE '%AM' THEN 
                CASE 
                    WHEN CAST(SUBSTR(start_time, 1, 2) AS INTEGER) = 12 THEN '00'
                    ELSE SUBSTR(start_time, 1, 2)
                END
        END || SUBSTR(start_time, 3, 3) >= ?", [now()->format('Y-m-d H:i:s')])
            ->get();

        Log::info('Horarios encontrados: ' . $availableSlots->count());

        $events = [];

        foreach ($availableSlots as $slot) {
            $date = Carbon::parse($slot->date)->format('Y-m-d');
            $startDateTime = Carbon::parse($date . ' ' . $slot->start_time);
            $endDateTime = Carbon::parse($date . ' ' . $slot->end_time);

            $events[] = [
                'id' => $slot->id,
                'title' => 'Disponible: ' . $startDateTime->format('H:i') . ' - ' . $endDateTime->format('H:i'),
                'start' => $startDateTime->format('Y-m-d H:i:s'),
                'end' => $endDateTime->format('Y-m-d H:i:s'),
                'backgroundColor' => '#22c55e',
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

    public function onEventClick($event): void
    {
        Log::info('Evento click recibido con ID: ' . $event['id']);

        $schedule = Schedule::find($event['id']);

        if (!$schedule) {
            $this->notify('error', 'No se pudo encontrar el horario seleccionado.');
            return;
        }

        $dateOnly = Carbon::parse($schedule->date)->format('Y-m-d');
        $startDateTime = Carbon::parse($dateOnly . ' ' . $schedule->start_time);
        Log::info('Fecha y hora de inicio parseada: ' . $startDateTime->toDateTimeString());
        if ($startDateTime->isPast()) {
            $this->notify('error', 'No se puede agendar una cita en un horario pasado.');
            return;
        }

        try {
            $date = Carbon::parse($schedule->date)->format('Y-m-d');
            $dateFormatted = Carbon::parse($schedule->date)->format('d/m/Y');
            $startTime = Carbon::createFromFormat('h:i A', $schedule->start_time);
            $endTime = Carbon::createFromFormat('h:i A', $schedule->end_time);

            $startDateTime = Carbon::parse($date)->setTime($startTime->hour, $startTime->minute, 0);
            $endDateTime = Carbon::parse($date)->setTime($endTime->hour, $endTime->minute, 0);

            $startTimeValue = $startDateTime->format('Y-m-d H:i:s');
            $endTimeValue = $endDateTime->format('Y-m-d H:i:s');

            $startTimeFormatted = $startTime->format('H:i');
            $endTimeFormatted = $endTime->format('H:i');

            $this->selectedScheduleId = $schedule->id;
            $this->selectedDate = $dateFormatted;
            $this->selectedStartTime = $startTimeValue;
            $this->selectedEndTime = $endTimeValue;
            $this->selectedStartTimeFormatted = $startTimeFormatted;
            $this->selectedEndTimeFormatted = $endTimeFormatted;
            $this->appointmentNotes = null;
            $this->selectedPriceId = null;

            Log::info('Datos asignados a propiedades del widget:', [
                'schedule_id' => $this->selectedScheduleId,
                'date' => $this->selectedDate,
                'start_time' => $this->selectedStartTime,
                'end_time' => $this->selectedEndTime,
                'start_time_formatted' => $this->selectedStartTimeFormatted,
                'end_time_formatted' => $this->selectedEndTimeFormatted,
            ]);

            $this->mountAction('create');
        } catch (\Exception $e) {
            Log::error('Error al procesar las horas: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            $this->notify('error', 'Error al procesar el horario seleccionado: ' . $e->getMessage());
        }
    }

    public function getFormSchema(): array
    {
        return [
            Forms\Components\Hidden::make('schedule_id')
                ->default(fn() => $this->selectedScheduleId)
                ->required(),

            Forms\Components\Hidden::make('user_id')
                ->default(function () {
                    if ($this->selectedScheduleId) {
                        $schedule = Schedule::find($this->selectedScheduleId);
                        return $schedule ? $schedule->user_id : null;
                    }
                    return null;
                })
                ->required(),

            Forms\Components\Hidden::make('client_id')
                ->default(fn() => auth()->user() instanceof \App\Models\Client ? auth()->user()->id : null)
                ->required(),

            Forms\Components\Section::make('Detalles de la Cita')
                ->description('Información principal de la cita')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('client_name')
                                ->label('Cliente')
                                ->default(fn() => auth()->user()->name ?? 'Cliente no autenticado')
                                ->readOnly()
                                ->required(),

                            Forms\Components\Select::make('schedule_id_display')
                                ->label('Horario')
                                ->options(function () {
                                    return Schedule::where('user_id', $this->record->id)
                                        ->where('is_available', true)
                                        ->pluck('date', 'id')
                                        ->map(fn($date) => \Carbon\Carbon::parse($date)->format('d/m/Y'));
                                })
                                ->default(fn() => $this->selectedScheduleId)
                                ->disabled()
                                ->dehydrated(false)
                                ->required(),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\DateTimePicker::make('start_time')
                                ->label('Hora de Inicio')
                                ->default(fn() => $this->selectedStartTime)
                                ->seconds(false)
                                ->minutesStep(15)
                                ->displayFormat('d/m/Y H:i')
                                ->timezone('America/Bogota')
                                ->native(false)
                                ->readOnly()
                                ->required(),

                            Forms\Components\DateTimePicker::make('end_time')
                                ->label('Hora de Fin')
                                ->default(fn() => $this->selectedEndTime)
                                ->seconds(false)
                                ->minutesStep(15)
                                ->displayFormat('d/m/Y H:i')
                                ->timezone('America/Bogota')
                                ->after('start_time')
                                ->native(false)
                                ->readOnly()
                                ->required(),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                        ]),
                ]),

            Forms\Components\Tabs::make('Detalles Adicionales')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('Estado y Notas')
                        ->icon('heroicon-o-clipboard-document-check')
                        ->schema([
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\Select::make('status')
                                        ->label('Estado')
                                        ->options([
                                            'pending' => 'Pendiente',
                                            'confirmed' => 'Confirmada',
                                        ])
                                        ->default('pending')
                                        ->disabled()
                                        ->required(),

                                    Forms\Components\Select::make('payment_status')
                                        ->label('Estado del Pago')
                                        ->options([
                                            'pending' => 'Pendiente',
                                        ])
                                        ->default('pending')
                                        ->disabled()
                                        ->required(),
                                ]),

                            Forms\Components\Textarea::make('notes')
                                ->label('Motivo o Notas de la Cita')
                                ->placeholder('Describe brevemente el motivo de tu cita...')
                                ->maxLength(500)
                                ->default(fn() => $this->appointmentNotes)
                                ->columnSpanFull(),
                        ]),
                ]),
        ];
    }

    public function getActions(): array
    {
        return [
            \Filament\Actions\Action::make('create')
                ->label('Reservar cita')
                ->form($this->getFormSchema())
                ->action(function (array $data) {
                    $this->create($data);
                })
                ->modalHeading('Reservar cita')
                ->modalSubmitActionLabel('Confirmar reserva')
        ];
    }

    public function create(array $data): void
    {
        Log::info('Creando cita con datos: ' . json_encode($data));

        try {
            $schedule = Schedule::findOrFail($data['schedule_id']);
            if (!$schedule->is_available) {
                throw new \Exception('El horario seleccionado ya no está disponible.');
            }
            $startDateTime = Carbon::parse($schedule->date . ' ' . $schedule->start_time);
            if ($startDateTime->isPast()) {
                throw new \Exception('No se puede agendar una cita en un horario pasado.');
            }
            $client = auth()->user();
            if (!$client || !$client instanceof \App\Models\Client) {
                throw new \Exception('No hay un cliente autenticado para reservar la cita.');
            }

            $appointment = Appointment::create([
                'user_id' => $schedule->user_id,
                'client_id' => $client->id,
                'schedule_id' => $schedule->id,
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'notes' => $data['notes'] ?? null,
                'status' => 'pending',
            ]);
            $schedule->update(['is_available' => false]);
            $this->notify('success', 'Cita reservada correctamente. Continúa con el pago.');
            $this->redirect(route('client.payment.process', ['appointment' => $appointment->id]));
        } catch (\Exception $e) {
            Log::error('Error al crear la cita: ' . $e->getMessage());
            $this->notify('error', 'Error al crear la cita: ' . $e->getMessage());
        }
    }
}