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
                                    ->reactive()
                                    ->columnSpan(1)
                                    ->weekStartsOnMonday()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        if ($state) {
                                            $dayOfWeek = date('N', strtotime($state));
                                            if ($dayOfWeek > 5) {
                                                $set('date', null);
                                                \Filament\Notifications\Notification::make()
                                                    ->title('Fecha no válida')
                                                    ->body('Solo se pueden crear citas de lunes a viernes.')
                                                    ->danger()
                                                    ->send();
                                            } else {
                                                // Verificar si hay horarios existentes al cambiar la fecha
                                                self::checkOverlappingSchedules($state, $get('start_time'), $get('end_time'), $get('user_id'), $set);
                                            }
                                        }
                                    }),

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
                                    ->reactive()
                                    ->validationMessages([
                                        'required' => 'La hora de inicio es obligatoria',
                                    ])
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        if ($state && $get('date') && $get('end_time')) {
                                            self::checkOverlappingSchedules($get('date'), $state, $get('end_time'), $get('user_id'), $set);
                                        }
                                    }),

                                Forms\Components\TimePicker::make('end_time')
                                    ->label('Hora de Fin')
                                    ->required()
                                    ->seconds(false)
                                    ->native(false)
                                    ->minutesStep(30)
                                    ->hoursStep(1)
                                    ->format('h:i A')
                                    ->reactive()
                                    ->validationMessages([
                                        'required' => 'La hora de fin es obligatoria',
                                    ])
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        // Validar que la hora de fin sea posterior a la de inicio
                                        $startTime = $get('start_time');
                                        if ($startTime && $state) {
                                            $start = strtotime($startTime);
                                            $end = strtotime($state);

                                            if ($end <= $start) {
                                                $set('end_time', null);
                                                \Filament\Notifications\Notification::make()
                                                    ->title('Horario inválido')
                                                    ->body('La hora de fin debe ser posterior a la hora de inicio.')
                                                    ->danger()
                                                    ->send();
                                            } else if ($get('date')) {
                                                // Verificar si hay horarios existentes al cambiar la hora de fin
                                                self::checkOverlappingSchedules($get('date'), $startTime, $state, $get('user_id'), $set);
                                            }
                                        }
                                    }),
                            ]),

                        Forms\Components\Toggle::make('is_available')
                            ->label('Disponible para citas')
                            ->helperText('Marque esta opción si este horario está disponible para que los clientes puedan reservar citas')
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_url')
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl(function ($record) {
                        // Mostrar avatar por defecto si no hay URL
                        return 'https://ui-avatars.com/api/?name=' . urlencode(User::find($record->user_id)->name ?? 'Usuario');
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

                Tables\Columns\IconColumn::make('is_available')
                    ->label('Disponible')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
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
                    ->options([
                        '1' => 'Disponible',
                        '0' => 'No disponible',
                    ])
                    ->placeholder('Todos los estados'),

                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('Desde')
                            ->placeholder(fn($state): string => now()->subMonth()->format('d/m/Y')),
                        Forms\Components\DatePicker::make('date_until')
                            ->label('Hasta')
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
                    ->preload()
                    ->visible(function() {
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
                        $record->is_available = !$record->is_available;
                        $record->save();

                        \Filament\Notifications\Notification::make()
                            ->title('Disponibilidad actualizada')
                            ->body('El estado de disponibilidad ha sido actualizado correctamente.')
                            ->success()
                            ->send();
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

    protected static function checkOverlappingSchedules($date, $startTime, $endTime, $userId, $set)
    {
        if (!$date || !$startTime || !$endTime || !$userId) {
            return;
        }

        $recordId = null;

        try {
            // Método alternativo para obtener el registro actual
            if (request()->route('record')) {
                $recordId = request()->route('record');
            } elseif (request()->has('record')) {
                $recordId = request()->input('record');
            }
        } catch (\Exception $e) {
            // Si hay algún error, continuamos sin excluir ningún registro
        }

        // Convertir los tiempos a objetos DateTime para comparar
        $start = strtotime($startTime);
        $end = strtotime($endTime);

        // Buscar horarios existentes para la misma fecha y usuario
        $existingSchedules = Schedule::where('date', $date)
            ->where('user_id', $userId)
            ->when($recordId, function ($query) use ($recordId) {
                // Excluir el registro actual si estamos editando
                return $query->where('id', '!=', $recordId);
            })
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
                // Hay superposición, mostrar notificación
                \Filament\Notifications\Notification::make()
                    ->title('Horario superpuesto')
                    ->body('Ya existe un horario para este profesional en esta fecha que se superpone con el horario seleccionado.')
                    ->danger()
                    ->send();

                $set('start_time', null);
                $set('end_time', null);

                return;
            }
        }
    }
}
