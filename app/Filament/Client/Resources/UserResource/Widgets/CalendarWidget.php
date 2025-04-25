<?php

namespace App\Filament\Client\Resources\UserResource\Widgets;

use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Models\Schedule;
use App\Models\Appointment;
use Carbon\Carbon;
use Filament\Forms;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Support\Exceptions\Cancel;
use Filament\Support\Exceptions\Halt;

class CalendarWidget extends FullCalendarWidget implements HasActions
{
    use InteractsWithActions;

    public Model|string|int|null $record = null;

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
            // Formatear la fecha para el calendario
            $date = Carbon::parse($slot->date)->format('Y-m-d');

            // Convertir horas a formato adecuado para el calendario
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

        $schedule = Schedule::find($event['id']);

        if (!$schedule) {
            $this->notify('error', 'No se pudo encontrar el horario seleccionado.');
            return;
        }

        try {
            // Parsear la fecha a formato Y-m-d para guardar
            $date = Carbon::parse($schedule->date)->format('Y-m-d');

            // Fecha formateada para mostrar
            $dateFormatted = Carbon::parse($schedule->date)->format('d/m/Y');

            // Crear objetos Carbon para las horas de inicio y fin
            $startTime = Carbon::createFromFormat('h:i A', $schedule->start_time);
            $endTime = Carbon::createFromFormat('h:i A', $schedule->end_time);

            // Combinar fecha y hora para crear datetime completos
            $startDateTime = Carbon::parse($date)->setTime($startTime->hour, $startTime->minute, 0);
            $endDateTime = Carbon::parse($date)->setTime($endTime->hour, $endTime->minute, 0);

            // Formato para guardar
            $startTimeValue = $startDateTime->format('Y-m-d H:i:s');
            $endTimeValue = $endDateTime->format('Y-m-d H:i:s');

            // Formato para mostrar
            $startTimeFormatted = $startTime->format('H:i');
            $endTimeFormatted = $endTime->format('H:i');

            // Asignar valores a las propiedades del widget
            $this->selectedScheduleId = $schedule->id;
            $this->selectedDate = $dateFormatted;
            $this->selectedStartTime = $startTimeValue;
            $this->selectedEndTime = $endTimeValue;
            $this->selectedStartTimeFormatted = $startTimeFormatted;
            $this->selectedEndTimeFormatted = $endTimeFormatted;
            $this->appointmentNotes = null; // Resetear notas

            Log::info('Datos asignados a propiedades del widget:', [
                'schedule_id' => $this->selectedScheduleId,
                'date' => $this->selectedDate,
                'start_time' => $this->selectedStartTime,
                'end_time' => $this->selectedEndTime,
                'start_time_formatted' => $this->selectedStartTimeFormatted,
                'end_time_formatted' => $this->selectedEndTimeFormatted,
            ]);

            // Mostrar el modal de la acción
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
                ->default(fn() => $this->selectedScheduleId),
            Forms\Components\Section::make('Detalles de la cita')
                ->description('Información sobre el horario seleccionado')
                ->schema([
                    Forms\Components\TextInput::make('date')
                        ->label('Fecha')
                        ->default(fn() => $this->selectedDate)
                        ->readOnly()
                        ->required(),
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('start_time_formatted')
                                ->label('Hora de inicio')
                                ->default(fn() => $this->selectedStartTimeFormatted)
                                ->readOnly()
                                ->required(),
                            Forms\Components\TextInput::make('end_time_formatted')
                                ->label('Hora de fin')
                                ->default(fn() => $this->selectedEndTimeFormatted)
                                ->readOnly()
                                ->required(),
                        ]),
                    // Campos ocultos para mantener los valores originales
                    Forms\Components\Hidden::make('start_time')
                        ->default(fn() => $this->selectedStartTime),
                    Forms\Components\Hidden::make('end_time')
                        ->default(fn() => $this->selectedEndTime),
                    Forms\Components\Textarea::make('notes')
                        ->label('Motivo o notas de la cita')
                        ->placeholder('Describe brevemente el motivo de tu cita...')
                        ->maxLength(500)
                        ->default(fn() => $this->appointmentNotes)
                        ->columnSpanFull(),
                ]),
        ];
    }

    protected function getActions(): array
    {
        return [
            Action::make('create')
                ->label('Reservar cita')
                ->form($this->getFormSchema())
                ->action(function (array $data) {
                    Log::info('Datos recibidos en la acción create:', $data);
                    $this->create($data);
                })
                ->modalHeading('Reservar cita')
                ->modalSubmitActionLabel('Confirmar reserva')
        ];
    }

    // Método create para crear la cita
    public function create(array $data): void
    {
        Log::info('Creando cita con datos: ' . json_encode($data));

        try {
            $schedule = Schedule::findOrFail($data['schedule_id']);

            // Asegúrate de que estos campos coincidan con los fillable en el modelo Appointment
            $appointment = Appointment::create([
                'user_id' => $schedule->user_id,
                'client_id' => auth()->user()->id,
                'schedule_id' => $schedule->id,
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'notes' => $data['notes'] ?? null,
                'status' => 'pending',
                'payment_status' => 'pending',
            ]);

            // Actualizar el schedule para marcarlo como no disponible
            $schedule->update(['is_available' => false]);

            $this->notify('success', 'Cita reservada correctamente. Continúa con el pago.');
            $this->redirect(route('client.payment.process', ['appointment' => $appointment->id]));
        } catch (\Exception $e) {
            Log::error('Error al crear la cita: ' . $e->getMessage());
            $this->notify('error', 'Error al crear la cita: ' . $e->getMessage());
        }
    }
}