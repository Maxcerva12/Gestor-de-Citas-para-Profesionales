<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use AymanAlhattami\FilamentPageWithSidebar\FilamentPageSidebar;
use AymanAlhattami\FilamentPageWithSidebar\PageNavigationItem;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Filament\Forms\Components\SectionHeader;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;
use App\Services\RoleManagerService;
use Illuminate\Support\Facades\Cache;

class UserResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = User::class;

    // Navigation and labels
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Profesionales';
    protected static ?string $navigationGroup = 'Gestión Administrativa';
    protected static ?string $label = 'Profesional';
    protected static ?string $pluralLabel = 'Profesionales';
    protected static ?string $recordTitleAttribute = 'name';

    // Navigation sorting
    protected static ?int $navigationSort = -10;

    // Default sort for data
    protected static ?string $defaultSort = 'name';

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

    // Permission checks
    public static function canViewAny(): bool
    {
        return Auth::check() && Gate::allows('view_any_user');
    }

    public static function canView(Model $record): bool
    {
        return Auth::check() && Gate::allows('view_user', $record);
    }

    public static function canCreate(): bool
    {
        return Auth::check() && Gate::allows('create_user');
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::check() && Gate::allows('update_user', $record);
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::check() && Gate::allows('delete_user', $record);
    }

    public static function canDeleteAny(): bool
    {
        return Auth::check() && Gate::allows('delete_any_user');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Gate::allows('view_any_user');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'last_name', 'email', 'document_number', 'profession', 'especialty'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->name . ' ' . $record->last_name;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Email' => $record->email,
            'Especialidad' => $record->especialty,
            'Profesión' => $record->profession,
        ];
    }

    /**
     * Obtiene las categorías de roles organizadas
     */
    public static function getRoleCategories(): array
    {
        return RoleManagerService::getRoleCategories();
    }

    /**
     * Obtiene los nombres de roles para una categoría específica
     */
    public static function getRoleNamesForCategory(string $category): array
    {
        return RoleManagerService::getRoleNamesForCategory($category);
    }

    /**
     * Obtiene todos los roles categorizados (excluyendo 'Otros')
     */
    public static function getCategorizedRoleNames(): array
    {
        return RoleManagerService::getCategorizedRoleNames();
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
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Select::make('document_type')
                                                    ->label('Tipo de Documento')
                                                    ->native(false)
                                                    ->options([
                                                        'CC' => 'Cédula de Ciudadanía',
                                                        'CE' => 'Cédula de Extranjería',
                                                        'TI' => 'Tarjeta de Identidad',
                                                        'PP' => 'Pasaporte',
                                                    ])
                                                    ->required(),
                                                Forms\Components\TextInput::make('document_number')
                                                    ->label('Número de Documento')
                                                    ->required()
                                                    ->maxLength(20),
                                            ]),
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('phone')
                                                    ->label('Teléfono')
                                                    ->tel()
                                                    ->prefix('+57')
                                                    ->telRegex('/^[0-9\s\-\+\(\)]+$/')
                                                    ->maxLength(20),
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
                                        Forms\Components\Textarea::make('address')
                                            ->label('Dirección')
                                            ->rows(2)
                                            ->maxLength(255),
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
                        Forms\Components\Tabs\Tab::make('Cuenta')
                            ->icon('heroicon-o-lock-closed')
                            ->schema([
                                Forms\Components\Section::make('Datos de acceso')
                                    ->schema([
                                        Forms\Components\TextInput::make('email')
                                            ->email()
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(ignoreRecord: true),
                                        Forms\Components\TextInput::make('password')
                                            ->password()
                                            ->label('Contraseña')
                                            ->required()
                                            ->revealable()
                                            ->maxLength(255)
                                            ->hiddenOn('edit')
                                            ->dehydrateStateUsing(fn($state) => filled($state) ? Hash::make($state) : null)
                                            ->dehydrated(fn($state) => filled($state))
                                            ->required(fn(string $operation): bool => $operation === 'create')
                                            ->helperText('Dejar en blanco para mantener la contraseña actual'),
                                        Forms\Components\Select::make('roles')
                                            ->label('Cargo')
                                            ->required()
                                            ->searchable()
                                            ->multiple()
                                            ->relationship('roles', 'name')
                                            ->options(function () {
                                                $rolesByName = Role::all()->keyBy('name');
                                                $groupedOptions = [];

                                                // Usar el servicio centralizado para obtener opciones agrupadas
                                                $categories = RoleManagerService::getRoleCategories();

                                                // Procesar roles categorizados
                                                foreach ($categories as $categoryName => $roleMap) {
                                                    foreach ($roleMap as $roleName => $roleLabel) {
                                                        if ($rolesByName->has($roleName)) {
                                                            $role = $rolesByName->get($roleName);
                                                            $groupedOptions[$categoryName][$role->id] = $roleLabel;
                                                        }
                                                    }
                                                }

                                                // Añadir roles no categorizados en "Otros"
                                                $categorizedRoleNames = RoleManagerService::getCategorizedRoleNames();
                                                $uncategorizedRoles = $rolesByName->whereNotIn('name', $categorizedRoleNames);
                                                if ($uncategorizedRoles->isNotEmpty()) {
                                                    foreach ($uncategorizedRoles as $role) {
                                                        $prettyName = RoleManagerService::getPrettyName($role->name);
                                                        $groupedOptions['Otros'][$role->id] = $prettyName;
                                                    }
                                                }

                                                return $groupedOptions;
                                            })
                                            ->native(false)
                                            ->preload()
                                            ->allowHtml(),
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
                Tables\Columns\ImageColumn::make('avatar_url')
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl(fn($record): string => "https://ui-avatars.com/api/?name=" . urlencode($record->name . " " . $record->last_name) . "&color=FFFFFF&background=6366F1")
                    ->size(40),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->sortable()
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->formatStateUsing(fn($record) => $record->name . ' ' . $record->last_name),
                Tables\Columns\TextColumn::make('profession')
                    ->label('Profesión')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('especialty')
                    ->label('Especialidad')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->color('success')
                    ->toggleable()
                    ->formatStateUsing(function ($state, $record) {
                        // $state puede ser string o array dependiendo de la relación
                        $roles = $record->roles ?? [];
                        if ($roles instanceof \Illuminate\Support\Collection) {
                            $roles = $roles->pluck('name')->toArray();
                        } elseif (is_object($roles)) {
                            $roles = collect($roles)->pluck('name')->toArray();
                        } elseif (!is_array($roles)) {
                            $roles = [$roles];
                        }

                        $labels = [];
                        foreach ($roles as $roleName) {
                            $labels[] = RoleManagerService::getPrettyName($roleName);
                        }
                        return implode(', ', $labels);
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->native(false)
                    ->preload(fn() => \Spatie\Permission\Models\Role::count() < 20)
                    ->placeholder('Todos los roles'),

                Tables\Filters\SelectFilter::make('profession')
                    ->native(false)
                    ->label('Profesión')
                    ->placeholder('Todas las profesiones')
                    ->options(function () {
                        return Cache::remember('user_professions_filter', 3600, function () {
                            return User::query()
                                ->whereNotNull('profession')
                                ->where('profession', '!=', '')
                                ->distinct()
                                ->orderBy('profession')
                                ->pluck('profession', 'profession')
                                ->toArray();
                        });
                    }),
                // ❌ QUITAR ->query() - El filtro automático funciona bien

                Tables\Filters\SelectFilter::make('especialty')
                    ->native(false)
                    ->label('Especialidad')
                    ->placeholder('Todas las especialidades')
                    ->options(function () {
                        return Cache::remember('user_especialties_filter', 3600, function () {
                            return User::query()
                                ->whereNotNull('especialty')
                                ->where('especialty', '!=', '')
                                ->distinct()
                                ->orderBy('especialty')
                                ->pluck('especialty', 'especialty')
                                ->toArray();
                        });
                    }),
                // ❌ QUITAR ->query() - El filtro automático funciona bien

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->native(false)
                            ->label('Creado desde')
                            ->placeholder('dd/mm/yyyy')
                            ->maxDate(fn(callable $get) => $get('created_until')),
                        Forms\Components\DatePicker::make('created_until')
                            ->native(false)
                            ->label('Creado hasta')
                            ->placeholder('dd/mm/yyyy')
                            ->minDate(fn(callable $get) => $get('created_from')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn($q, $date) => $q->where('created_at', '>=', $date . ' 00:00:00')
                            )
                            ->when(
                                $data['created_until'],
                                fn($q, $date) => $q->where('created_at', '<=', $date . ' 23:59:59')
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators[] = 'Creado desde: ' . \Carbon\Carbon::parse($data['created_from'])->format('d/m/Y');
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators[] = 'Creado hasta: ' . \Carbon\Carbon::parse($data['created_until'])->format('d/m/Y');
                        }

                        return $indicators;
                    })
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('gray'),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                    ->tooltip('Acciones')
                    ->icon('heroicon-m-ellipsis-vertical'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('exportToCsv')
                        ->label('Exportar seleccionados')
                        ->icon('heroicon-o-document-arrow-down')
                        ->action(function (Collection $records) {
                            return response()->streamDownload(function () use ($records) {
                                $csv = fopen('php://output', 'w');

                                // Add headers
                                fputcsv($csv, ['ID', 'Nombre', 'Apellidos', 'Email', 'Profesión', 'Especialidad', 'Teléfono']);

                                // Add data rows
                                foreach ($records as $record) {
                                    fputcsv($csv, [
                                        $record->id,
                                        $record->name,
                                        $record->last_name,
                                        $record->email,
                                        $record->profession ?? '',
                                        $record->especialty ?? '',
                                        $record->phone ?? '',
                                    ]);
                                }

                                fclose($csv);
                            }, 'profesionales-' . date('Y-m-d') . '.csv');
                        }),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Crear Profesional'),
            ])
            ->emptyStateDescription('Comienza agregando un nuevo profesional a tu sistema.')
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'dental-staff' => Pages\DentalStaffList::route('/dental-staff'),
            'admin-staff' => Pages\AdminStaffList::route('/admin-staff'),
            'management' => Pages\ManagementList::route('/management'),
            'other-roles' => Pages\OtherRolesList::route('/other-roles'),

        ];
    }

    // Modificamos el método sidebar para que no dependa de $record
    public static function sidebar(): FilamentPageSidebar
    {
        return FilamentPageSidebar::make()
            ->setTitle('Gestión de Personal')
            ->setDescription('Administra todo el personal de la clínica odontológica')
            ->setNavigationItems([
                PageNavigationItem::make('Todos los Usuarios')
                    ->url(static::getUrl('index'))
                    ->icon('heroicon-o-users')
                    ->isActiveWhen(fn() => request()->routeIs('filament.resources.users.index')),

                PageNavigationItem::make('Personal Odontológico')
                    ->url(static::getUrl('dental-staff'))
                    ->icon('heroicon-o-user-circle')
                    ->isActiveWhen(fn() => request()->routeIs('filament.resources.users.dental-staff')),

                PageNavigationItem::make('Personal Administrativo')
                    ->url(static::getUrl('admin-staff'))
                    ->icon('heroicon-o-user')
                    ->isActiveWhen(fn() => request()->routeIs('filament.resources.users.admin-staff')),

                PageNavigationItem::make('Gerencia')
                    ->url(static::getUrl('management'))
                    ->icon('heroicon-o-briefcase')
                    ->isActiveWhen(fn() => request()->routeIs('filament.resources.users.management')),
                PageNavigationItem::make('Otros Roles')
                    ->url(static::getUrl('other-roles'))
                    ->icon('heroicon-o-plus-circle')
                    ->isActiveWhen(fn() => request()->routeIs('filament.resources.users.other-roles')),
            ]);
    }
}
