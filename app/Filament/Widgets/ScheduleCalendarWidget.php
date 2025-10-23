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
            $dateStr = Carbon::parse($schedule->date)->format('Y-m-d');
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
                'past' => 'Horario Pasado',
                'booked' => 'Reservado',
                'unavailable' => 'No Disponible',
                'available' => 'Disponible',
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
            $tooltip = "{$profesionalInfo}\nFecha: " . Carbon::parse($schedule->date)->format('d/m/Y') .
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
     * Define el formulario para solo ver los detalles del horario (solo lectura).
     */
    public function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Información del Horario')
                ->description('Detalles del horario seleccionado')
                ->schema([
                    Forms\Components\TextInput::make('user.name')
                        ->label('Profesional')
                        ->disabled()
                        ->formatStateUsing(function ($record) {
                            if (!$record || !$record->user)
                                return 'No disponible';

                            $name = $record->user->name;
                            if ($record->user->last_name) {
                                $name .= ' ' . $record->user->last_name;
                            }

                            $extraInfo = [];
                            if ($record->user->especialty) {
                                $extraInfo[] = $record->user->especialty;
                            }
                            if ($record->user->profession) {
                                $extraInfo[] = $record->user->profession;
                            }

                            if (!empty($extraInfo)) {
                                $name .= ' (' . implode(' - ', $extraInfo) . ')';
                            }

                            return $name;
                        }),

                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\DatePicker::make('date')
                                ->label('Fecha')
                                ->disabled()
                                ->displayFormat('d/m/Y'),

                            Forms\Components\TimePicker::make('start_time')
                                ->label('Hora de Inicio')
                                ->disabled()
                                ->displayFormat('H:i'),

                            Forms\Components\TimePicker::make('end_time')
                                ->label('Hora de Fin')
                                ->disabled()
                                ->displayFormat('H:i'),
                        ])->columns(3),
                ]),

            Forms\Components\Section::make('Estado de Disponibilidad')
                ->schema([
                    Forms\Components\Toggle::make('is_available')
                        ->label('Disponible para citas')
                        ->disabled()
                        ->formatStateUsing(function ($record) {
                            if (!$record)
                                return false;

                            // Verificar si el horario ya pasó
                            $dateStr = Carbon::parse($record->date)->format('Y-m-d');
                            $startTimeStr = Carbon::parse($record->start_time)->format('H:i:s');
                            $startDateTime = Carbon::parse("$dateStr $startTimeStr");
                            $isPast = $startDateTime->isPast();

                            // Verificar si está reservado
                            $isBooked = $record->appointments()
                                ->where('status', 'confirmed')
                                ->exists();

                            // Disponible solo si no ha pasado el horario, no está reservado y está marcado como disponible
                            return !$isPast && !$isBooked && $record->is_available;
                        })
                        ->onColor('success')
                        ->offColor('danger'),

                    Forms\Components\TextInput::make('status_display')
                        ->label('Estado del horario')
                        ->disabled()
                        ->formatStateUsing(function ($record) {
                            if (!$record)
                                return 'Estado no disponible';

                            // Verificar si el horario ya pasó
                            $dateStr = Carbon::parse($record->date)->format('Y-m-d');
                            $startTimeStr = Carbon::parse($record->start_time)->format('H:i:s');
                            $startDateTime = Carbon::parse("$dateStr $startTimeStr");
                            $isPast = $startDateTime->isPast();

                            // Verificar si está reservado
                            $isBooked = $record->appointments()
                                ->where('status', 'confirmed')
                                ->exists();

                            return match (true) {
                                $isPast => 'Horario pasado - No disponible',
                                $isBooked => 'Horario reservado - No disponible',
                                !$record->is_available => 'Marcado como no disponible',
                                default => 'Disponible para citas',
                            };
                        })
                        ->prefixIcon(function ($record) {
                            if (!$record)
                                return 'heroicon-o-information-circle';

                            // Verificar si el horario ya pasó
                            $dateStr = Carbon::parse($record->date)->format('Y-m-d');
                            $startTimeStr = Carbon::parse($record->start_time)->format('H:i:s');
                            $startDateTime = Carbon::parse("$dateStr $startTimeStr");
                            $isPast = $startDateTime->isPast();

                            // Verificar si está reservado
                            $isBooked = $record->appointments()
                                ->where('status', 'confirmed')
                                ->exists();

                            return match (true) {
                                $isPast => 'heroicon-o-clock',
                                $isBooked => 'heroicon-o-check-circle',
                                !$record->is_available => 'heroicon-o-x-circle',
                                default => 'heroicon-o-check-badge',
                            };
                        })
                        ->prefixIconColor(function ($record) {
                            if (!$record)
                                return 'gray';

                            // Verificar si el horario ya pasó
                            $dateStr = Carbon::parse($record->date)->format('Y-m-d');
                            $startTimeStr = Carbon::parse($record->start_time)->format('H:i:s');
                            $startDateTime = Carbon::parse("$dateStr $startTimeStr");
                            $isPast = $startDateTime->isPast();

                            // Verificar si está reservado
                            $isBooked = $record->appointments()
                                ->where('status', 'confirmed')
                                ->exists();

                            return match (true) {
                                $isPast => 'gray',
                                $isBooked => 'info',
                                !$record->is_available => 'danger',
                                default => 'success',
                            };
                        }),

                    Forms\Components\Textarea::make('notes')
                        ->label('Notas')
                        ->disabled()
                        ->placeholder('Sin notas adicionales')
                        ->visible(fn($record) => $record && !empty($record->notes)),
                ]),
        ];
    }



    /**
     * Configuración del calendario para solo visualización.
     */
    public function config(): array
    {
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
            // Configuraciones para solo visualización
            'selectable' => false,
            'editable' => false,
            'eventResizableFromStart' => false,
            'eventDurationEditable' => false,
            'eventStartEditable' => false,
            'droppable' => false,
            'eventOverlap' => false,
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
     * Sin acciones disponibles en el encabezado (solo visualización).
     */
    protected function headerActions(): array
    {
        return [];
    }

    /**
     * Solo acción de visualización disponible en el modal.
     */
    protected function modalActions(): array
    {
        return [
            \Saade\FilamentFullCalendar\Actions\ViewAction::make()
                ->modalHeading('Detalles del horario')
                ->modalWidth('lg')
                ->icon('heroicon-o-eye')
                ->label('Ver detalles')
                ->color('info'),
        ];
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