<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\Schedule;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Support\Enums\FontWeight;
use App\Filament\Client\Pages\ProfessionalAvailability;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;



class UserResource extends Resource
{
    protected static ?string $model = User::class;

    // Navigation and labels
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Profesionales';
    protected static ?string $navigationGroup = 'Agendamientos de Citas';
    protected static ?string $label = 'Profesional';
    protected static ?string $pluralLabel = 'Profesionales';
    protected static ?string $recordTitleAttribute = 'name';

    // Navigation sorting
    protected static ?int $navigationSort = -10;

    // Default sort for data
    protected static ?string $defaultSort = 'name';

    /**
     * Optimización: Aplicar eager loading y consultas agregadas
     * Compatible con PostgreSQL y MySQL
     */
    public static function getEloquentQuery(): Builder
    {
        $today = Carbon::today()->toDateString();
        $driver = config('database.default');
        
        return parent::getEloquentQuery()
            ->select([
                'users.*',
                // Subconsulta para contar horarios disponibles (evita N+1)
                DB::raw('(SELECT COUNT(*) 
                    FROM schedules 
                    WHERE schedules.user_id = users.id 
                    AND schedules.is_available = true 
                    AND schedules.date >= \'' . $today . '\'
                    AND NOT EXISTS (
                        SELECT 1 FROM appointments 
                        WHERE appointments.schedule_id = schedules.id 
                        AND appointments.status IN (\'pending\', \'confirmed\')
                    )
                ) as available_slots_count'),
                
                // Subconsulta para total de horarios futuros
                DB::raw('(SELECT COUNT(*) 
                    FROM schedules 
                    WHERE schedules.user_id = users.id 
                    AND schedules.date >= \'' . $today . '\'
                ) as total_schedules_count'),
                
                // Subconsulta para horarios ocupados
                DB::raw('(SELECT COUNT(*) 
                    FROM schedules 
                    WHERE schedules.user_id = users.id 
                    AND schedules.date >= \'' . $today . '\'
                    AND EXISTS (
                        SELECT 1 FROM appointments 
                        WHERE appointments.schedule_id = schedules.id 
                        AND appointments.status IN (\'pending\', \'confirmed\')
                    )
                ) as occupied_schedules_count')
            ])
            ->with(['schedules' => function ($query) {
                // Solo cargar horarios futuros y disponibles si es necesario
                $query->where('date', '>=', Carbon::today())
                    ->where('is_available', true)
                    ->limit(1); // Solo verificar si existe al menos uno
            }]);
    }

    public static function canCreate(): bool
    {
        return false; // Nadie puede crear usuarios desde este recurso
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Información Personal')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Forms\Components\Section::make('Datos Básicos')
                                    ->description('Información básica del profesional')
                                    ->collapsible()
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Nombre')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('last_name')
                                                    ->label('Apellidos')
                                                    ->required()
                                                    ->maxLength(255),
                                            ]),
                                        Forms\Components\Grid::make(2),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\FileUpload::make('avatar_url')
                                                    ->label('Foto de perfil')
                                                    ->image()
                                                    ->directory('avatars')
                                                    ->visibility('public')
                                                    ->imageEditor(),
                                            ]),
                                    ]),
                                Forms\Components\Section::make('Dirección')
                                    ->description('Datos de ubicación')
                                    ->collapsible()
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('city')
                                                    ->label('Ciudad')
                                                    ->maxLength(100),
                                                Forms\Components\TextInput::make('country')
                                                    ->label('País')
                                                    ->maxLength(100),
                                            ]),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('Información Profesional')
                            ->icon('heroicon-o-briefcase')
                            ->schema([
                                Forms\Components\Section::make('Datos Profesionales')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('profession')
                                                    ->label('Profesión')
                                                    ->required()
                                                    ->maxLength(100),
                                                Forms\Components\TextInput::make('especialty')
                                                    ->label('Especialidad')
                                                    ->required()
                                                    ->maxLength(100),
                                            ]),
                                        Forms\Components\Textarea::make('description')
                                            ->label('Descripción')
                                            ->rows(3)
                                            ->maxLength(500)
                                            ->helperText('Breve descripción profesional'),
                                        Forms\Components\KeyValue::make('custom_fields')
                                            ->label('Campos Personalizados')
                                            ->keyLabel('Campo')
                                            ->valueLabel('Valor')
                                            ->addButtonLabel('Añadir campo')
                                            ->reorderable(),
                                    ]),
                            ]),
                    ])
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Layout horizontal: Avatar + Información
                Tables\Columns\Layout\Split::make([
                    // Avatar sin borde
                    Tables\Columns\ImageColumn::make('avatar_url')
                        ->label('')
                        ->circular()
                        ->defaultImageUrl(fn($record): string => 
                            "https://ui-avatars.com/api/?name=" . urlencode($record->name . " " . $record->last_name) . 
                            "&color=FFFFFF&background=6366F1&bold=true&size=256")
                        ->size(50)
                        ->grow(false),

                    // Información del profesional (stack vertical dentro del split horizontal)
                    Tables\Columns\Layout\Stack::make([
                        // Nombre del profesional
                        Tables\Columns\TextColumn::make('name')
                            ->label('Profesional')
                            ->searchable(['name', 'last_name'])
                            ->sortable()
                            ->weight(FontWeight::Bold)
                            ->size(Tables\Columns\TextColumn\TextColumnSize::Medium)
                            ->formatStateUsing(fn($record) => $record->name . ' ' . $record->last_name)
                            ->icon('heroicon-m-user-circle')
                            ->iconColor('primary'),

                        // Fila de badges: Profesión, Especialidad, Disponibilidad y Espacios
                        Tables\Columns\Layout\Split::make([
                            Tables\Columns\TextColumn::make('profession')
                                ->badge()
                                ->color('info')
                                ->icon('heroicon-m-briefcase')
                                ->size(Tables\Columns\TextColumn\TextColumnSize::Small)
                                ->grow(false),

                            Tables\Columns\TextColumn::make('especialty')
                                ->badge()
                                ->color('success')
                                ->icon('heroicon-m-academic-cap')
                                ->size(Tables\Columns\TextColumn\TextColumnSize::Small)
                                ->grow(false),

                            // Badge de disponibilidad (OPTIMIZADO: usa datos precalculados)
                            Tables\Columns\TextColumn::make('availability_status')
                                ->badge()
                                ->getStateUsing(function (User $record): string {
                                    // Usa el conteo precalculado del query principal
                                    $availableSchedules = $record->available_slots_count ?? 0;

                                    if ($availableSchedules > 10) {
                                        return 'Alta disponibilidad';
                                    } elseif ($availableSchedules > 0) {
                                        return 'Disponible';
                                    } else {
                                        return 'No disponible';
                                    }
                                })
                                ->color(fn(string $state): string => match ($state) {
                                    'Alta disponibilidad' => 'success',
                                    'Disponible' => 'warning',
                                    'No disponible' => 'danger',
                                    default => 'gray',
                                })
                                ->icon(fn(string $state): string => match ($state) {
                                    'Alta disponibilidad' => 'heroicon-m-check-circle',
                                    'Disponible' => 'heroicon-m-clock',
                                    'No disponible' => 'heroicon-m-x-circle',
                                    default => 'heroicon-m-question-mark-circle',
                                })
                                ->size(Tables\Columns\TextColumn\TextColumnSize::Small)
                                ->grow(false),

                            // Badge de espacios disponibles (OPTIMIZADO: usa datos precalculados)
                            Tables\Columns\TextColumn::make('available_slots')
                                ->badge()
                                ->getStateUsing(function (User $record): int {
                                    // Usa el conteo precalculado del query principal
                                    return $record->available_slots_count ?? 0;
                                })
                                ->color(fn(int $state): string => match (true) {
                                    $state > 10 => 'success',
                                    $state > 0 => 'warning',
                                    default => 'danger',
                                })
                                ->formatStateUsing(fn(int $state): string => $state . ' disponibles')
                                ->size(Tables\Columns\TextColumn\TextColumnSize::Small)
                                ->grow(false),
                        ]),

                        // Descripción (opcional)
                        Tables\Columns\TextColumn::make('description')
                            ->label('Descripción')
                            ->limit(100)
                            ->tooltip(fn($record): ?string => $record->description)
                            ->color('gray')
                            ->size(Tables\Columns\TextColumn\TextColumnSize::Small)
                            ->icon('heroicon-m-information-circle')
                            ->visible(fn($record) => !empty($record->description)),
                    ])->space(1),
                ])->from('md'),
            ])
            ->filters([
                // OPTIMIZADO: Usa caché para evitar escaneos repetidos de tabla
                Tables\Filters\SelectFilter::make('profession')
                    ->options(function () {
                        return cache()->remember('user_professions', 3600, function () {
                            return User::whereNotNull('profession')
                                ->distinct()
                                ->orderBy('profession')
                                ->pluck('profession', 'profession')
                                ->toArray();
                        });
                    })
                    ->label('Filtrar por Profesión')
                    ->placeholder('Todas las profesiones')
                    ->native(false)
                    ->searchable()
                    ->preload(),
                
                // OPTIMIZADO: Usa caché para evitar escaneos repetidos de tabla
                Tables\Filters\SelectFilter::make('especialty')
                    ->options(function () {
                        return cache()->remember('user_especialties', 3600, function () {
                            return User::whereNotNull('especialty')
                                ->distinct()
                                ->orderBy('especialty')
                                ->pluck('especialty', 'especialty')
                                ->toArray();
                        });
                    })
                    ->label('Filtrar por Especialidad')
                    ->placeholder('Todas las especialidades')
                    ->native(false)
                    ->searchable()
                    ->preload(),

                // OPTIMIZADO: Filtra correctamente usando subconsulta
                Tables\Filters\TernaryFilter::make('has_availability')
                    ->label('Con disponibilidad')
                    ->placeholder('Todos los profesionales')
                    ->trueLabel('Solo con disponibilidad')
                    ->falseLabel('Solo sin disponibilidad')
                    ->queries(
                        true: function (Builder $query) {
                            $today = Carbon::today()->toDateString();
                            $query->whereRaw('EXISTS (
                                SELECT 1 FROM schedules 
                                WHERE schedules.user_id = users.id 
                                AND schedules.is_available = true 
                                AND schedules.date >= \'' . $today . '\'
                                AND NOT EXISTS (
                                    SELECT 1 FROM appointments 
                                    WHERE appointments.schedule_id = schedules.id 
                                    AND appointments.status IN (\'pending\', \'confirmed\')
                                )
                            )');
                        },
                        false: function (Builder $query) {
                            $today = Carbon::today()->toDateString();
                            $query->whereRaw('NOT EXISTS (
                                SELECT 1 FROM schedules 
                                WHERE schedules.user_id = users.id 
                                AND schedules.is_available = true 
                                AND schedules.date >= \'' . $today . '\'
                                AND NOT EXISTS (
                                    SELECT 1 FROM appointments 
                                    WHERE appointments.schedule_id = schedules.id 
                                    AND appointments.status IN (\'pending\', \'confirmed\')
                                )
                            )');
                        },
                    )
                    ->native(false),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)
            ->persistFiltersInSession()
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('Ver perfil completo')
                        ->icon('heroicon-o-identification')
                        ->color('info')
                        ->modalHeading(fn($record) => 'Perfil de ' . $record->name . ' ' . $record->last_name)
                        ->modalWidth('5xl')
                        ->modalIcon('heroicon-o-user-circle')
                        ->slideOver(),
                    
                    Tables\Actions\Action::make('ver_disponibilidad')
                        ->label('Ver disponibilidad')
                        ->icon('heroicon-o-calendar-days')
                        ->color('success')
                        ->url(fn(User $record) => static::getUrl('view-availability', ['record' => $record]))
                        ->openUrlInNewTab(false),
                    
                    Tables\Actions\Action::make('contact_info')
                        ->label('Información de contacto')
                        ->icon('heroicon-o-phone')
                        ->color('warning')
                        ->modalContent(fn(User $record) => view('filament.client.modals.contact-info', ['user' => $record]))
                        ->modalHeading('Contactar profesional')
                        ->modalIcon('heroicon-o-envelope')
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Cerrar'),
                ])
                ->tooltip('Acciones')
                ->icon('heroicon-m-ellipsis-vertical')
                ->color('primary')
                ->button(),
            ])
            ->emptyStateIcon('heroicon-o-magnifying-glass')
            ->emptyStateHeading('No se encontraron profesionales')
            ->emptyStateDescription('No hay profesionales que cumplan con los filtros seleccionados. Intenta ajustar o eliminar algunos filtros para ver más resultados.')
            ->defaultSort('name', 'asc')
            ->poll('120s') // OPTIMIZADO: Auto-actualizar cada 2 minutos (antes 30s)
            ->deferLoading()
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession()
            ->striped()
            ->paginated([12, 24, 48]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Sección de encabezado con avatar y nombre
                Infolists\Components\Section::make()
                    ->schema([
                        Infolists\Components\Split::make([
                            Infolists\Components\Grid::make(2)
                                ->schema([
                                    Infolists\Components\Group::make([
                                        Infolists\Components\TextEntry::make('full_name')
                                            ->label('Nombre completo')
                                            ->getStateUsing(fn($record) => $record->name . ' ' . $record->last_name)
                                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                            ->weight(FontWeight::Bold)
                                            ->icon('heroicon-m-user')
                                            ->iconColor('primary'),

                                        Infolists\Components\TextEntry::make('email')
                                            ->label('Correo electrónico')
                                            ->icon('heroicon-m-envelope')
                                            ->iconColor('success')
                                            ->copyable()
                                            ->copyMessage('Correo copiado')
                                            ->copyMessageDuration(1500),

                                        Infolists\Components\TextEntry::make('phone')
                                            ->label('Teléfono')
                                            ->icon('heroicon-m-phone')
                                            ->iconColor('info')
                                            ->placeholder('No especificado'),
                                    ]),

                                    Infolists\Components\ImageEntry::make('avatar_url')
                                        ->label('')
                                        ->circular()
                                        ->defaultImageUrl(fn($record): string => 
                                            "https://ui-avatars.com/api/?name=" . urlencode($record->name . " " . $record->last_name) . 
                                            "&color=FFFFFF&background=6366F1&bold=true&size=512")
                                        ->height(150)
                                        ->alignCenter(),
                                ]),
                        ]),
                    ])
                    ->columnSpan('full'),

                // Información profesional
                Infolists\Components\Section::make('Información Profesional')
                    ->icon('heroicon-o-briefcase')
                    ->description('Credenciales y especialización')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('profession')
                                    ->label('Profesión')
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-m-briefcase'),

                                Infolists\Components\TextEntry::make('especialty')
                                    ->label('Especialidad')
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-m-academic-cap'),

                                Infolists\Components\TextEntry::make('years_experience')
                                    ->label('Años de experiencia')
                                    ->placeholder('No especificado')
                                    ->icon('heroicon-m-chart-bar'),
                            ]),

                        Infolists\Components\TextEntry::make('description')
                            ->label('Sobre mí')
                            ->markdown()
                            ->placeholder('Este profesional no ha agregado una descripción.')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                // Ubicación
                Infolists\Components\Section::make('Ubicación')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('city')
                                    ->label('Ciudad')
                                    ->icon('heroicon-m-building-office-2')
                                    ->placeholder('No especificado'),

                                Infolists\Components\TextEntry::make('country')
                                    ->label('País')
                                    ->icon('heroicon-m-globe-alt')
                                    ->placeholder('No especificado'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),

                // Estadísticas de disponibilidad
                Infolists\Components\Section::make('Disponibilidad')
                    ->icon('heroicon-o-calendar')
                    ->description('Estado actual de horarios disponibles')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                // OPTIMIZADO: Usa datos precalculados del query
                        Infolists\Components\TextEntry::make('total_schedules')
                            ->label('Total de horarios')
                            ->getStateUsing(fn($record) => $record->total_schedules_count ?? 0)
                            ->badge()
                            ->color('info')
                            ->icon('heroicon-m-calendar-days'),

                        Infolists\Components\TextEntry::make('available_schedules')
                            ->label('Horarios disponibles')
                            ->getStateUsing(fn($record) => $record->available_slots_count ?? 0)
                            ->badge()
                            ->color('success')
                            ->icon('heroicon-m-check-circle'),

                        Infolists\Components\TextEntry::make('occupied_schedules')
                            ->label('Horarios ocupados')
                            ->getStateUsing(fn($record) => $record->occupied_schedules_count ?? 0)
                            ->badge()
                            ->color('warning')
                            ->icon('heroicon-m-clock'),
                            ]),
                    ])
                    ->collapsible(),

                // Campos personalizados
                Infolists\Components\Section::make('Información Adicional')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Infolists\Components\KeyValueEntry::make('custom_fields')
                            ->label('')
                            ->hiddenLabel()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn($record) => !empty($record->custom_fields))
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'view-availability' => Pages\ViewAvailability::route('/{record}/disponibilidad'),
        ];
    }
}