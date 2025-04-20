<?php

namespace App\Filament\Widgets;

use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Models\Schedule;
use App\Models\Appointment;
use App\Models\User;
use Filament\Forms;
use Illuminate\Support\Facades\Auth;
use Saade\FilamentFullCalendar\Data\EventData;
use Illuminate\Database\Eloquent\Model;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;

class ScheduleCalendarWidget extends FullCalendarWidget
{
    use HasWidgetShield;

    public Model|string|null $model = Schedule::class;

    // Cambio de título más descriptivo
    protected static ?string $heading = 'Agenda de Disponibilidad';

    // Definimos los colores como constantes para mantener consistencia
    const COLOR_PAST = '#9ca3af';        // Gris - horario pasado
    const COLOR_BOOKED = '#3b82f6';      // Azul - reservado
    const COLOR_UNAVAILABLE = '#ef4444'; // Rojo - no disponible
    const COLOR_AVAILABLE = '#22c55e';   // Verde - disponible

    /**
     * Determina si el widget debería ser visible para el usuario actual
     */
    public static function canView(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // El super_admin siempre puede ver el widget
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Cualquier usuario con permisos relevantes puede ver el widget
        return $user->hasPermissionTo('view_schedule_calendar') ||
            $user->hasPermissionTo('manage_schedules') ||
            $user->hasRole('professional');
    }

    /**
     * Determina si el widget debería registrarse en la navegación
     */
    public static function shouldRegisterNavigation(): bool
    {
        return static::canView();
    }

    /**
     * Recupera los horarios para mostrar en el calendario.
     * Muestra todos los horarios para administradores o solo los del profesional autenticado.
     */
    public function fetchEvents(array $fetchInfo): array
    {
        // Optimizar la consulta con eager loading
        $query = Schedule::query()
            ->with([
                'user' => function ($q) {
                    $q->select('id', 'name', 'last_name', 'profession', 'especialty');
                },
                'appointments' => function ($q) {
                    $q->where('status', 'confirmed')->select('id', 'schedule_id', 'status');
                }
            ])
            ->whereBetween('date', [$fetchInfo['start'], $fetchInfo['end']]);

        // Filtrar según permisos
        $user = Auth::user();

        // super_admin o usuario con permiso especial puede ver todos los horarios
        if (!$user->hasRole('super_admin') && !$user->hasPermissionTo('view_all_schedules')) {
            $query->where('user_id', $user->id);
        }

        $now = Carbon::now();
        $events = [];

        foreach ($query->get() as $schedule) {
            // Crear objetos Carbon para mejor manipulación de fechas
            $dateStr = $schedule->date->format('Y-m-d');
            $startTimeStr = Carbon::parse($schedule->start_time)->format('H:i:s');
            $endTimeStr = Carbon::parse($schedule->end_time)->format('H:i:s');

            // Combinar fecha y hora correctamente
            $startDateTime = Carbon::parse("$dateStr $startTimeStr");
            $endDateTime = Carbon::parse("$dateStr $endTimeStr");

            // Determinar estados y colores
            $isPast = $startDateTime->isPast();
            $isBooked = $schedule->appointments->isNotEmpty();

            $status = match (true) {
                $isPast => 'past',
                $isBooked => 'booked',
                !$schedule->is_available => 'unavailable',
                default => 'available',
            };

            // Configurar color según estado
            $backgroundColor = match ($status) {
                'past' => self::COLOR_PAST,
                'booked' => self::COLOR_BOOKED,
                'unavailable' => self::COLOR_UNAVAILABLE,
                'available' => self::COLOR_AVAILABLE,
            };

            // Configurar título según estado
            $title = match ($status) {
                'past' => '⏱️ Horario Pasado',
                'booked' => '🔵 Reservado',
                'unavailable' => '❌ No Disponible',
                'available' => '✅ Disponible',
            };

            // Información del profesional con formato mejorado
            $profesionalInfo = $schedule->user->name;
            if ($schedule->user->last_name) {
                $profesionalInfo .= ' ' . $schedule->user->last_name;
            }

            // Añadir especialidad y profesión si existen
            $extraInfo = [];
            if ($schedule->user->especialty) {
                $extraInfo[] = $schedule->user->especialty;
            }
            if ($schedule->user->profession) {
                $extraInfo[] = $schedule->user->profession;
            }

            if (!empty($extraInfo)) {
                $profesionalInfo .= ' (' . implode(' - ', $extraInfo) . ')';
            }

            // Información para el tooltip
            $tooltip = "{$profesionalInfo}\nFecha: " . $schedule->date->format('d/m/Y') .
                "\nHorario: " . Carbon::parse($schedule->start_time)->format('H:i') .
                " - " . Carbon::parse($schedule->end_time)->format('H:i');

            // Información extra para el tooltip según estado
            if ($status === 'booked') {
                $tooltip .= "\nEste horario ya está reservado";
            } elseif ($status === 'unavailable') {
                $tooltip .= "\nEste horario no está disponible para citas";
            }

            // Crear el evento para el calendario con mejor usabilidad
            $events[] = EventData::make()
                ->id($schedule->id)
                ->title("{$title}")
                ->start($startDateTime)
                ->end($endDateTime)
                ->backgroundColor($backgroundColor)
                ->borderColor(darkenColor($backgroundColor, 15))
                ->textColor('#ffffff')
                // Configurando todas las propiedades extendidas juntas
                ->extendedProps([
                    'profesionalInfo' => $profesionalInfo,
                    'canEdit' => !$isPast && $this->canEditSchedule($schedule),
                    'selectable' => !$isPast,
                    'status' => $status,
                    'tooltip' => $tooltip // Moviendo el tooltip a extendedProps
                ])
                ->toArray();
        }

        return $events;
    }

    /**
     * Determina si un usuario puede editar un horario específico
     */
    protected function canEditSchedule(Schedule $schedule): bool
    {
        $user = Auth::user();

        // Super admin puede editar cualquier horario
        if ($user && $user->hasRole('super_admin')) {
            return true;
        }

        // Usuarios con permiso para editar cualquier horario
        if ($user && $user->hasPermissionTo('edit_all_schedules')) {
            return true;
        }

        // Los usuarios solo pueden editar sus propios horarios no reservados
        return $user && $schedule->user_id === $user->id &&
            $schedule->appointments->isEmpty() &&
            ($user->hasPermissionTo('manage_schedules') || $user->hasRole('professional'));
    }

    /**
     * Define el formulario para crear/editar horarios desde el calendario.
     */
    public function getFormSchema(): array
    {
        $user = Auth::user();
        $schema = [];

        // Verificar permisos de gestión
        $canManageSchedules = $user->hasRole('super_admin') ||
            $user->hasPermissionTo('manage_schedules') ||
            $user->hasRole('professional');

        // Solo usuarios con permisos pueden ver/editar el horario
        if ($canManageSchedules) {
            // Si el usuario es super_admin o tiene permiso especial, mostrar selector de profesional
            if ($user->hasRole('super_admin') || $user->hasPermissionTo('edit_all_schedules')) {
                $schema[] = Forms\Components\Select::make('user_id')
                    ->label('Profesional')
                    ->relationship(
                        name: 'user',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn(Builder $query) => $query
                    )
                    ->getOptionLabelFromRecordUsing(
                        fn(Model $record) =>
                        $record->name . ' ' . $record->last_name .
                        ($record->especialty ? " ({$record->especialty})" : '')
                    )
                    ->searchable()
                    ->preload()
                    ->required();
            } else {
                // Para otros usuarios, solo pueden asignar a sí mismos
                $schema[] = Forms\Components\Hidden::make('user_id')
                    ->default($user->id);
            }

            // Resto del esquema de formulario común con mejor organización
            $schema = array_merge($schema, [
                Forms\Components\Section::make('Información del Horario')
                    ->description('Establezca la fecha y hora de disponibilidad')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\DatePicker::make('date')
                                    ->label('Fecha')
                                    ->required()
                                    ->minDate(now())
                                    ->displayFormat('d/m/Y')
                                    ->weekStartsOnMonday()
                                    ->native(false)
                                    ->reactive(),

                                Forms\Components\TimePicker::make('start_time')
                                    ->label('Hora de Inicio')
                                    ->required()
                                    ->seconds(false)
                                    ->minutesStep(30)
                                    ->displayFormat('H:i')
                                    ->native(false)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, $get) {
                                        $this->validateTimeRange($state, $get('end_time'), $set);
                                    }),

                                Forms\Components\TimePicker::make('end_time')
                                    ->label('Hora de Fin')
                                    ->required()
                                    ->seconds(false)
                                    ->minutesStep(30)
                                    ->displayFormat('H:i')
                                    ->native(false)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, $get) {
                                        $this->validateTimeRange($get('start_time'), $state, $set);
                                    }),
                            ])->columns(3),
                    ]),

                Forms\Components\Section::make('Configuración de Disponibilidad')
                    ->schema([
                        Forms\Components\Toggle::make('is_available')
                            ->label('Disponible para citas')
                            ->helperText('Marque esta opción si este horario está disponible para reservas')
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger'),

                        Forms\Components\Textarea::make('notes')
                            ->label('Notas')
                            ->placeholder('Añada información adicional sobre este horario (opcional)')
                            ->maxLength(255),
                    ]),
            ]);
        }

        return $schema;
    }

    /**
     * Valida que la hora de fin sea posterior a la hora de inicio.
     */
    protected function validateTimeRange($startTime, $endTime, $set): void
    {
        if ($startTime && $endTime) {
            $startDateTime = Carbon::parse($startTime);
            $endDateTime = Carbon::parse($endTime);

            if ($endDateTime->lte($startDateTime)) {
                $set('end_time', null);
                $this->notify('error', 'La hora de fin debe ser posterior a la hora de inicio');
            } elseif ($endDateTime->diffInMinutes($startDateTime) < 30) {
                $set('end_time', $startDateTime->copy()->addMinutes(30)->format('H:i'));
                $this->notify('warning', 'La duración mínima del horario debe ser de 30 minutos');
            }
        }
    }

    /**
     * Configuración adicional del calendario con mejor UX.
     */
    public function config(): array
    {
        $user = Auth::user();

        // Verificar permisos de gestión
        $canManageSchedules = $user->hasRole('super_admin') ||
            $user->hasPermissionTo('manage_schedules') ||
            $user->hasRole('professional');

        return [
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,timeGridDay,listWeek',
            ],
            'initialView' => 'timeGridWeek',
            'slotDuration' => '00:30:00',
            'slotMinTime' => '07:00:00',
            'slotMaxTime' => '21:00:00',
            'businessHours' => [
                'daysOfWeek' => [1, 2, 3, 4, 5], // Lunes a viernes
                'startTime' => '08:00',
                'endTime' => '18:00',
            ],
            'nowIndicator' => true,
            'dayMaxEvents' => true,
            'eventTimeFormat' => [
                'hour' => '2-digit',
                'minute' => '2-digit',
                'hour12' => false,
            ],
            'eventDisplay' => 'auto',
            'displayEventEnd' => true,
            'weekNumbers' => true,
            'weekNumberCalculation' => 'ISO',
            'firstDay' => 1, // Lunes
            'locale' => 'es',
            'timeZone' => config('app.timezone'),
            'selectable' => true,
            'editable' => $canManageSchedules,
            'eventOverlap' => false,
            'eventResizableFromStart' => true,
            'eventClick' => [
                'display' => 'auto', // Muestra el modal al hacer clic
            ],
            'views' => [
                'dayGridMonth' => [
                    'dayMaxEventRows' => 4,
                    'dayHeaderFormat' => ['weekday' => 'short'],
                ],
                'timeGridWeek' => [
                    'dayHeaderFormat' => ['weekday' => 'short', 'day' => 'numeric'],
                    'slotEventOverlap' => false,
                ],
                'timeGridDay' => [
                    'dayHeaderFormat' => ['weekday' => 'long', 'day' => 'numeric', 'month' => 'long'],
                ],
                'listWeek' => [
                    'listDayFormat' => ['weekday' => 'long', 'day' => 'numeric', 'month' => 'long'],
                    'listDaySideFormat' => false,
                ],
            ],
            'eventContent' => [
                'html' => true, // Permitir HTML en el contenido del evento
            ],
            'height' => 'auto', // Altura adaptable
            'themeSystem' => 'standard',
            'buttonText' => [
                'today' => 'Hoy',
                'month' => 'Mes',
                'week' => 'Semana',
                'day' => 'Día',
                'list' => 'Lista'
            ],
        ];
    }

    /**
     * Acciones disponibles en el encabezado.
     */
    protected function headerActions(): array
    {
        $user = Auth::user();

        // Verificar permisos de gestión
        $canManageSchedules = $user->hasRole('super_admin') ||
            $user->hasPermissionTo('manage_schedules') ||
            $user->hasRole('professional');

        // Solo mostrar el botón de crear si el usuario tiene los permisos adecuados
        if (!$canManageSchedules) {
            return [];
        }

        return [
            \Saade\FilamentFullCalendar\Actions\CreateAction::make()
                ->modalHeading('Crear nuevo horario')
                ->modalWidth('lg')
                ->modalAlignment('center')
                ->icon('heroicon-o-plus-circle')
                ->color('success')
                ->label('Nuevo horario')
                ->successNotificationTitle('Horario creado correctamente'),
        ];
    }

    /**
     * Acciones disponibles en el modal al interactuar con un evento.
     */
    protected function modalActions(): array
    {
        $user = Auth::user();
        $actions = [];

        // Verificar permisos de gestión
        $canManageSchedules = $user->hasRole('super_admin') ||
            $user->hasPermissionTo('manage_schedules') ||
            $user->hasRole('professional');

        // Vista detallada siempre disponible
        $actions[] = \Saade\FilamentFullCalendar\Actions\ViewAction::make()
            ->modalHeading('Detalles del horario')
            ->modalWidth('lg')
            ->icon('heroicon-o-eye')
            ->label('Ver detalles');

        // Editar solo si tiene permisos
        if ($canManageSchedules) {
            $actions[] = \Saade\FilamentFullCalendar\Actions\EditAction::make()
                ->modalHeading('Editar horario')
                ->modalWidth('lg')
                ->visible(function (array $arguments) use ($user) {
                    // Verificar que exista el ID
                    if (!isset($arguments['id'])) {
                        return false;
                    }

                    $schedule = Schedule::find($arguments['id']);
                    if (!$schedule) {
                        return false;
                    }

                    // Verificar si el horario está reservado
                    $isBooked = $schedule->appointments()
                        ->where('status', 'confirmed')
                        ->exists();

                    // Super admin o usuario con permiso especial puede editar cualquier horario no reservado
                    if ($user->hasRole('super_admin') || $user->hasPermissionTo('edit_all_schedules')) {
                        return !$isBooked;
                    }

                    // Usuarios solo pueden editar sus propios horarios no reservados
                    return $schedule->user_id === $user->id && !$isBooked;
                })
                ->icon('heroicon-o-pencil-square')
                ->color('primary')
                ->successNotificationTitle('Horario actualizado correctamente');

            $actions[] = \Saade\FilamentFullCalendar\Actions\DeleteAction::make()
                ->modalHeading('Eliminar horario')
                ->modalAlignment('center')
                ->modalWidth('sm')
                ->requiresConfirmation()
                ->visible(function (array $arguments) use ($user) {
                    // Verificar que exista el ID
                    if (!isset($arguments['id'])) {
                        return false;
                    }

                    $schedule = Schedule::find($arguments['id']);
                    if (!$schedule) {
                        return false;
                    }

                    // Verificar si el horario está reservado
                    $isBooked = $schedule->appointments()
                        ->where('status', 'confirmed')
                        ->exists();

                    // Super admin o usuario con permiso especial puede eliminar cualquier horario no reservado
                    if ($user->hasRole('super_admin') || $user->hasPermissionTo('delete_all_schedules')) {
                        return !$isBooked;
                    }

                    // Usuarios solo pueden eliminar sus propios horarios no reservados
                    return $schedule->user_id === $user->id && !$isBooked;
                })
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->modalDescription('¿Está seguro que desea eliminar este horario? Esta acción no se puede deshacer.')
                ->successNotificationTitle('Horario eliminado correctamente');
        }

        return $actions;
    }

    /**
     * Método para mostrar notificaciones.
     */
    protected function notify(string $type, string $message): void
    {
        $notificationMethod = match ($type) {
            'success' => 'success',
            'error' => 'danger',
            'warning' => 'warning',
            default => 'info',
        };

        Notification::make()
                    ->title($message)
            ->{$notificationMethod}()
                ->send();
    }
}

/**
 * Función auxiliar para oscurecer un color
 * @param string $hex Color en formato hexadecimal
 * @param int $percent Porcentaje de oscurecimiento (0-100)
 * @return string Color oscurecido en formato hexadecimal
 */
function darkenColor($hex, $percent)
{
    // Eliminar el signo # si existe
    $hex = ltrim($hex, '#');

    // Convertir a valores RGB
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));

    // Oscurecer
    $r = max(0, min(255, $r - round($r * ($percent / 100))));
    $g = max(0, min(255, $g - round($g * ($percent / 100))));
    $b = max(0, min(255, $b - round($b * ($percent / 100))));

    // Convertir de vuelta a hexadecimal
    return sprintf('#%02x%02x%02x', $r, $g, $b);
}