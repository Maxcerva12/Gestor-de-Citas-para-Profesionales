<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduleResource\Pages;
use App\Models\Schedule;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Support\Enums\FontWeight;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Horarios';
    protected static ?string $navigationGroup = 'Gestión de Citas';
    protected static ?string $label = 'Horario';
    protected static ?string $pluralLabel = 'Horarios';

    protected static int $sort = -19;
    protected static ?int $navigationSort = -19;

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::check() && Gate::allows('view_any_schedule');
    }

    public static function canView(Model $record): bool
    {
        return Auth::check() && Gate::allows('view_schedule', $record);
    }

    public static function canCreate(): bool
    {
        return Auth::check() && Gate::allows('create_schedule');
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::check() && Gate::allows('update_schedule', $record);
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::check() && Gate::allows('delete_schedule', $record);
    }

    public static function canDeleteAny(): bool
    {
        return Auth::check() && Gate::allows('delete_any_schedule');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Gate::allows('view_any_schedule');
    }

    /**
     * Restringe el resource a los horarios del usuario autenticado.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();


        // Si el usuario es superAdmin, mostrar todas las citas
        // Si no, mostrar solo las citas del usuario autenticado
        if (!Auth::user() || !Auth::user()->hasRole('super_admin')) {
            $query->where('user_id', Auth::id());
        }

        return $query->latest('date');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información del Horario')
                    ->description('Establezca la fecha y horario de disponibilidad')
                    ->icon('heroicon-o-clock')
                    ->collapsible()
                    ->schema([
                        Grid::make()
                            ->columns(2)
                            ->schema([
                                Forms\Components\Hidden::make('user_id')
                                    ->default(Auth::id()), // Asigna el ID del profesional autenticado

                                Forms\Components\DatePicker::make('date')
                                    ->label('Fecha')
                                    ->required()
                                    ->minDate(now()->startOfDay())
                                    ->native(false)
                                    ->format('Y-m-d')
                                    ->displayFormat('d/m/Y')
                                    ->columnSpan(1)
                                    ->weekStartsOnMonday(),

                                Forms\Components\Select::make('user_id')
                                    ->label('Profesional')
                                    ->options(function () {
                                        // Solo mostrar este campo para superadmin
                                        if (Auth::user() && Auth::user()->hasRole('super_admin')) {
                                            return User::whereHas('roles', function ($query) {
                                                $query->whereIn('name', ['professional', 'super_admin']);
                                            })->pluck('name', 'id');
                                        }
                                        return [];
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->columnSpan(1)
                                    ->hidden(fn() => !Auth::user() || !Auth::user()->hasRole('super_admin')),
                            ]),

                        Grid::make()
                            ->columns(2)
                            ->schema([
                                Forms\Components\TimePicker::make('start_time')
                                    ->label('Hora de Inicio')
                                    ->required()
                                    ->native(false)
                                    ->seconds(false)
                                    ->minutesStep(30)
                                    ->hoursStep(1)
                                    ->format('h:i A')
                                    ->validationMessages([
                                        'required' => 'La hora de inicio es obligatoria',
                                    ]),

                                Forms\Components\TimePicker::make('end_time')
                                    ->label('Hora de Fin')
                                    ->required()
                                    ->seconds(false)
                                    ->native(false)
                                    ->minutesStep(30)
                                    ->hoursStep(1)
                                    ->format('h:i A')
                                    ->validationMessages([
                                        'required' => 'La hora de fin es obligatoria',
                                    ]),
                            ]),

                        Forms\Components\Toggle::make('is_available')
                            ->label('Disponible para citas')
                            ->helperText('Marque esta opción si este horario está disponible para que los clientes puedan reservar citas')
                            ->default(true)
                            ->onColor('primary')
                            ->offColor('danger'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('user.avatar_url')
                    ->label('Avatar')
                    ->circular()
                    ->getStateUsing(function ($record) {
                        $user = User::find($record->user_id);
                        // Si el usuario tiene una imagen subida, usar esa
                        if ($user && $user->getFilamentAvatarUrl()) {
                            return $user->getFilamentAvatarUrl();
                        }
                        // Si no tiene imagen, usar el avatar por defecto
                        return 'https://ui-avatars.com/api/?name=' . urlencode($user->name ?? 'Usuario');
                    })
                    ->size(40),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Profesional')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Hora de Inicio')
                    ->time('h:i A') // Cambiado para mostrar formato 12 horas con AM/PM
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_time')
                    ->label('Hora de Fin')
                    ->time('h:i A') // Cambiado para mostrar formato 12 horas con AM/PM
                    ->sortable(),

                Tables\Columns\TextColumn::make('availability_status')
                    ->label('Estado')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        try {
                            if (!$record->is_available) {
                                return 'No disponible';
                            }

                            // Verificar si está expirado
                            $date = \Carbon\Carbon::parse($record->date)->format('Y-m-d');
                            $scheduleDateTime = \Carbon\Carbon::parse($date . ' ' . $record->start_time);
                            if ($scheduleDateTime->isPast()) {
                                return 'Expirado';
                            }

                            // Verificar si está ocupado
                            $hasAppointments = $record->appointments()
                                ->whereIn('status', ['pending', 'confirmed'])
                                ->exists();
                            if ($hasAppointments) {
                                return 'Ocupado';
                            }

                            return 'Disponible';
                        } catch (\Exception $e) {
                            \Log::error('Error en availability_status: ' . $e->getMessage());
                            return $record->is_available ? 'Disponible' : 'No disponible';
                        }
                    })
                    ->color(function ($record) {
                        try {
                            if (!$record->is_available) {
                                return 'danger';
                            }

                            $date = \Carbon\Carbon::parse($record->date)->format('Y-m-d');
                            $scheduleDateTime = \Carbon\Carbon::parse($date . ' ' . $record->start_time);
                            if ($scheduleDateTime->isPast()) {
                                return 'warning';
                            }

                            $hasAppointments = $record->appointments()
                                ->whereIn('status', ['pending', 'confirmed'])
                                ->exists();
                            if ($hasAppointments) {
                                return 'info';
                            }

                            return 'success';
                        } catch (\Exception $e) {
                            return $record->is_available ? 'success' : 'danger';
                        }
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('is_available')
                    ->label('Disponibilidad')
                    ->native(false)
                    ->options([
                        '1' => 'Disponible',
                        '0' => 'No disponible',
                    ])
                    ->placeholder('Todos los estados'),

                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('Desde')
                            ->native(false)
                            ->placeholder(fn($state): string => now()->subMonth()->format('d/m/Y')),
                        Forms\Components\DatePicker::make('date_until')
                            ->label('Hasta')
                            ->native(false)
                            ->placeholder(fn($state): string => now()->addMonths(2)->format('d/m/Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['date_from'] ?? null) {
                            $indicators['date_from'] = 'Desde ' . \Carbon\Carbon::parse($data['date_from'])->format('d/m/Y');
                        }

                        if ($data['date_until'] ?? null) {
                            $indicators['date_until'] = 'Hasta ' . \Carbon\Carbon::parse($data['date_until'])->format('d/m/Y');
                        }

                        return $indicators;
                    }),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Profesional')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->native(false)
                    ->preload()
                    ->visible(function () {
                        /** @var \App\Models\User $user */
                        $user = Auth::user();
                        return $user && $user->hasRole('super_admin');
                    }),

            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->tooltip('Editar horario')
                    ->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->tooltip('Eliminar horario')
                    ->iconButton(),
                Tables\Actions\Action::make('toggle_availability')
                    ->icon('heroicon-o-arrow-path')
                    ->tooltip('Cambiar disponibilidad')
                    ->iconButton()
                    ->color('warning')
                    ->action(function (Schedule $record) {
                        try {
                            \Log::info("Intentando cambiar disponibilidad del horario {$record->id}. Estado actual: " . ($record->is_available ? 'disponible' : 'no disponible'));

                            // Si se intenta activar un horario que está desactivado
                            if (!$record->is_available) {
                                // Verificar si el horario ha expirado
                                $date = \Carbon\Carbon::parse($record->date)->format('Y-m-d');
                                $scheduleDateTime = \Carbon\Carbon::parse($date . ' ' . $record->start_time);
                                \Log::info("Fecha/hora del horario: {$scheduleDateTime}, Ahora: " . \Carbon\Carbon::now());

                                if ($scheduleDateTime->isPast()) {
                                    \Log::info("Horario ha expirado, no se puede activar");
                                    \Filament\Notifications\Notification::make()
                                        ->title('Acción no permitida')
                                        ->body('No se puede activar un horario que ya ha expirado.')
                                        ->warning()
                                        ->send();
                                    return;
                                }

                                // Verificar si tiene citas confirmadas o pendientes (recargar relación)
                                $record->load('appointments');
                                $appointments = $record->appointments()
                                    ->whereIn('status', ['pending', 'confirmed'])
                                    ->get();

                                \Log::info("Citas encontradas: " . $appointments->count());

                                if ($appointments->count() > 0) {
                                    \Log::info("Horario tiene citas asignadas, no se puede activar");
                                    \Filament\Notifications\Notification::make()
                                        ->title('Acción no permitida')
                                        ->body('No se puede activar un horario que tiene citas asignadas.')
                                        ->warning()
                                        ->send();
                                    return;
                                }
                            }

                            // Cambiar el estado
                            $newStatus = !$record->is_available;
                            $record->update(['is_available' => $newStatus]);

                            \Log::info("Horario {$record->id} actualizado a: " . ($newStatus ? 'disponible' : 'no disponible'));

                            $status = $newStatus ? 'activado' : 'desactivado';

                            \Filament\Notifications\Notification::make()
                                ->title('Disponibilidad actualizada')
                                ->body("El horario ha sido {$status} correctamente.")
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            \Log::error('Error en toggle availability: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
                            \Filament\Notifications\Notification::make()
                                ->title('Error')
                                ->body('Ocurrió un error al cambiar la disponibilidad: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('toggle_bulk_availability')
                        ->label('Cambiar disponibilidad')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->is_available = !$record->is_available;
                                $record->save();
                            }

                            \Filament\Notifications\Notification::make()
                                ->title('Disponibilidad actualizada')
                                ->body('La disponibilidad de los horarios seleccionados ha sido actualizada.')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-calendar')
            ->emptyStateHeading('No hay horarios configurados')
            ->emptyStateDescription('Comience creando un nuevo horario de disponibilidad.')
            ->emptyStateActions([
                Tables\Actions\Action::make('create')
                    ->label('Crear horario')
                    ->url(route('filament.admin.resources.schedules.create'))
                    ->icon('heroicon-o-plus')
                    ->button(),
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchedules::route('/'),
            'create' => Pages\CreateSchedule::route('/create'),
            'edit' => Pages\EditSchedule::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('user_id', Auth::id())
            ->where('is_available', true)
            ->where('date', '>=', now()->format('Y-m-d'))
            ->count();
    }


}
