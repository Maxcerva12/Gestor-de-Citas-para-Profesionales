<?php

namespace App\Filament\Client\Resources\UserResource\Widgets;

use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Models\Schedule;
use App\Models\Appointment;
use App\Models\InvoiceSettings;
use Carbon\Carbon;
use Filament\Forms;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

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
    public $selectedServiceId = null;
    public $selectedServicePrice = null;
    public $selectedPaymentMethod = null;

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

        // Obtener solo horarios REALMENTE disponibles usando el scope del modelo
        $availableSlots = Schedule::where('user_id', $this->record->id)
            ->reallyAvailable()
            ->whereBetween('date', [$fetchInfo['start'], $fetchInfo['end']])
            ->get();

        Log::info('Horarios realmente disponibles encontrados: ' . $availableSlots->count());

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
            Notification::make()
                ->title('Error')
                ->body('No se pudo encontrar el horario seleccionado.')
                ->danger()
                ->send();
            return;
        }

        $dateOnly = Carbon::parse($schedule->date)->format('Y-m-d');
        $startDateTime = Carbon::parse($dateOnly . ' ' . $schedule->start_time);
        Log::info('Fecha y hora de inicio parseada: ' . $startDateTime->toDateTimeString());
        if ($startDateTime->isPast()) {
            Notification::make()
                ->title('Error')
                ->body('No se puede agendar una cita en un horario pasado.')
                ->danger()
                ->send();
            return;
        }

        try {
            $date = Carbon::parse($schedule->date)->format('Y-m-d');
            $dateFormatted = Carbon::parse($schedule->date)->format('d/m/Y');

            // Parsear las horas correctamente - pueden estar en formato H:i:s o H:i
            $startTime = Carbon::createFromFormat('H:i:s', $schedule->start_time)
                ?: Carbon::createFromFormat('H:i', $schedule->start_time);
            $endTime = Carbon::createFromFormat('H:i:s', $schedule->end_time)
                ?: Carbon::createFromFormat('H:i', $schedule->end_time);

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
            $this->selectedServiceId = null;
            $this->selectedServicePrice = null;
            $this->selectedPaymentMethod = null;

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
            Notification::make()
                ->title('Error')
                ->body('Error al procesar el horario seleccionado: ' . $e->getMessage())
                ->danger()
                ->send();
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
                                ->native(true)
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
                                ->native(true)
                                ->readOnly()
                                ->required(),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('service_id')
                                ->label('Servicio Requerido')
                                ->options(function () {
                                    if (!$this->record)
                                        return [];
                                    return \App\Models\Service::where('user_id', $this->record->id)
                                        ->where('is_active', true)
                                        ->pluck('name', 'id')
                                        ->map(function ($name, $id) {
                                            $service = \App\Models\Service::find($id);
                                            return $name . ' - $' . number_format((float) $service->price, 0, ',', '.');
                                        });
                                })
                                ->searchable()
                                ->preload()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if ($state) {
                                        $service = \App\Models\Service::find($state);
                                        if ($service) {
                                            $basePrice = (float) $service->price;
                                            
                                            // Obtener configuración de IVA y descuentos
                                            $taxRate = (float) InvoiceSettings::get('tax_rate', 19);
                                            $discountEnabled = InvoiceSettings::get('discount_enabled', 'false') === 'true';
                                            $discountPercentage = $discountEnabled ? (float) InvoiceSettings::get('discount_percentage', 0) : 0;
                                            
                                            // Calcular descuento
                                            $discountAmount = 0;
                                            if ($discountEnabled && $discountPercentage > 0) {
                                                $discountAmount = ($basePrice * $discountPercentage) / 100;
                                            }
                                            $priceAfterDiscount = $basePrice - $discountAmount;
                                            
                                            // Calcular IVA
                                            $taxAmount = ($priceAfterDiscount * $taxRate) / 100;
                                            $finalPrice = $priceAfterDiscount + $taxAmount;
                                            
                                            // Establecer valores
                                            $set('service_price', $finalPrice);
                                            $set('base_price', $basePrice);
                                            $set('discount_amount', $discountAmount);
                                            $set('tax_amount', $taxAmount);
                                            $set('tax_rate', $taxRate);
                                            $set('discount_percentage', $discountPercentage);
                                            
                                            $this->selectedServiceId = $state;
                                            $this->selectedServicePrice = $finalPrice;
                                        }
                                    } else {
                                        $set('service_price', null);
                                        $set('base_price', null);
                                        $set('discount_amount', null);
                                        $set('tax_amount', null);
                                        $set('tax_rate', null);
                                        $set('discount_percentage', null);
                                        $this->selectedServiceId = null;
                                        $this->selectedServicePrice = null;
                                    }
                                })
                                ->helperText('Selecciona el servicio que necesitas')
                                ->required(),

                            Forms\Components\TextInput::make('service_price')
                                ->label('Precio Total a Pagar (COP)')
                                ->prefix('$')
                                ->numeric()
                                ->readOnly()
                                ->placeholder('Selecciona un servicio')
                                ->helperText('Precio final con IVA y descuentos aplicados'),
                        ]),

                    // Campos ocultos para almacenar información del cálculo
                    Forms\Components\Hidden::make('base_price'),
                    Forms\Components\Hidden::make('discount_amount'),
                    Forms\Components\Hidden::make('tax_amount'),
                    Forms\Components\Hidden::make('tax_rate'),
                    Forms\Components\Hidden::make('discount_percentage'),

                    // Desglose de precios
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\Placeholder::make('base_price_display')
                                ->label('Precio Base')
                                ->content(function (callable $get) {
                                    $basePrice = $get('base_price');
                                    return $basePrice ? '$' . number_format($basePrice, 0, ',', '.') : 'N/A';
                                })
                                ->visible(fn (callable $get) => $get('service_id') !== null),

                            Forms\Components\Placeholder::make('discount_display')
                                ->label('Descuento Aplicado')
                                ->content(function (callable $get) {
                                    $discountAmount = $get('discount_amount');
                                    $discountPercentage = $get('discount_percentage');
                                    if ($discountAmount > 0) {
                                        return '-$' . number_format($discountAmount, 0, ',', '.') . ' (' . $discountPercentage . '%)';
                                    }
                                    return 'Sin descuento';
                                })
                                ->visible(fn (callable $get) => $get('service_id') !== null),

                            Forms\Components\Placeholder::make('tax_display')
                                ->label('IVA')
                                ->content(function (callable $get) {
                                    $taxAmount = $get('tax_amount');
                                    $taxRate = $get('tax_rate');
                                    return $taxAmount ? '+$' . number_format($taxAmount, 0, ',', '.') . ' (' . $taxRate . '%)' : 'N/A';
                                })
                                ->visible(fn (callable $get) => $get('service_id') !== null),
                        ])
                        ->visible(fn (callable $get) => $get('service_id') !== null),

                    Forms\Components\Grid::make(1)
                        ->schema([
                            Forms\Components\Select::make('payment_method')
                                ->label('Método de Pago Preferido')
                                ->options([
                                    'efectivo' => 'Efectivo',
                                    'transferencia' => 'Transferencia bancaria',
                                    'tarjeta_debito' => 'Tarjeta de débito',
                                ])
                                ->native(false)
                                ->reactive()
                                ->afterStateUpdated(function ($state) {
                                    $this->selectedPaymentMethod = $state;
                                })
                                ->helperText('Como prefieres pagar este servicio')
                                ->required(),
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

            // Verificar si el horario está realmente disponible
            if (!$schedule->isReallyAvailable()) {
                throw new \Exception('El horario seleccionado ya no está disponible o ha expirado.');
            }

            $client = auth()->user();
            if (!$client || !$client instanceof \App\Models\Client) {
                throw new \Exception('No hay un cliente autenticado para reservar la cita.');
            }

            // Verificar que no haya otra cita en este horario
            $existingAppointment = $schedule->appointments()
                ->whereIn('status', ['pending', 'confirmed'])
                ->first();

            if ($existingAppointment) {
                throw new \Exception('Este horario ya tiene una cita asignada.');
            }

            // Usar transacción para asegurar atomicidad
            $appointmentId = \DB::transaction(function () use ($data, $schedule, $client) {
                // Crear la cita
                $appointment = Appointment::create([
                    'user_id' => $schedule->user_id,
                    'client_id' => $client->id,
                    'schedule_id' => $schedule->id,
                    'service_id' => $data['service_id'] ?? null,
                    'service_price' => $data['service_price'] ?? null,
                    'payment_method' => $data['payment_method'] ?? 'efectivo',
                    'payment_status' => 'pending',
                    'start_time' => $data['start_time'],
                    'end_time' => $data['end_time'],
                    'notes' => $data['notes'] ?? null,
                    'status' => 'pending',
                ]);

                // Marcar horario como no disponible
                $schedule->markAsUnavailable();

                Log::info("Cita creada con ID: {$appointment->id}, horario {$schedule->id} marcado como no disponible");

                return $appointment->id;
            });

            Notification::make()
                ->title('¡Cita reservada exitosamente!')
                ->body('Su cita ha sido reservada. Continúe con el proceso de pago.')
                ->success()
                ->send();

            // Forzar recarga del calendario para mostrar cambios
            $this->dispatch('refresh-calendar');

            $this->redirect(route('client.payment.process', ['appointment' => $appointmentId]));

        } catch (\Exception $e) {
            Log::error('Error al crear la cita: ' . $e->getMessage());
            Notification::make()
                ->title('Error al reservar cita')
                ->body('Error: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}