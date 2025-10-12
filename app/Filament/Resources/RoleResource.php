<?php

namespace App\Filament\Resources;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use BezhanSalleh\FilamentShield\Forms\ShieldSelectAllToggle;
use App\Filament\Resources\RoleResource\Pages;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Traits\HasShieldFormComponents;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use App\Services\RoleManagerService;

class RoleResource extends Resource implements HasShieldPermissions
{
    use HasShieldFormComponents;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationBadgeTooltip = 'Total de roles en el sistema';

    protected static ?int $navigationSort = 1;

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica del Rol')
                    ->description('Configura los datos principales del rol de usuario')
                    ->icon('heroicon-o-identification')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('name')
                                    ->label('Nombre del Rol')
                                    ->placeholder('Selecciona o escribe un nuevo rol...')
                                    ->unique(ignoreRecord: true)
                                    ->required()
                                    ->searchable()
                                    ->allowHtml()
                                    ->prefixIcon('heroicon-o-tag')
                                    ->helperText('Selecciona un rol predefinido o escribe uno personalizado')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        // Solo aplicar title si es un rol personalizado (no está en las opciones predefinidas)
                                        $predefinedRoles = RoleManagerService::getCategorizedRoleNames();
                                        if (!in_array($state, $predefinedRoles)) {
                                            $set('name', Str::slug($state));
                                        }
                                    })
                                    ->options(function () {
                                        return RoleManagerService::getGroupedOptionsForFilament();
                                    })
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('custom_role_name')
                                            ->label('Nombre del Rol Personalizado')
                                            ->placeholder('Ej: Especialista en Implantes')
                                            ->required()
                                            ->maxLength(255)
                                            ->helperText('El nombre se convertirá automáticamente a formato slug (ej: especialista-en-implantes)'),
                                    ])
                                    ->createOptionUsing(function (array $data): string {
                                        return Str::slug($data['custom_role_name']);
                                    })
                                    ->getSearchResultsUsing(function (string $search): array {
                                        $options = [];

                                        // Obtener roles predefinidos que coincidan con la búsqueda
                                        $allRoles = RoleManagerService::getGroupedOptionsForFilament();
                                        foreach ($allRoles as $category => $roles) {
                                            foreach ($roles as $roleName => $roleLabel) {
                                                if (
                                                    str_contains(strtolower($roleLabel), strtolower($search)) ||
                                                    str_contains(strtolower($roleName), strtolower($search))
                                                ) {
                                                    $options[$roleName] = $roleLabel;
                                                }
                                            }
                                        }

                                        // Si el usuario está escribiendo algo y no hay coincidencias exactas, 
                                        // mostrar la opción de crear un rol personalizado
                                        if (!empty($search) && empty($options)) {
                                            $sluggedSearch = Str::slug($search);
                                            $titleSearch = Str::title($search);
                                            $options[$sluggedSearch] = "✨ Crear rol personalizado: {$titleSearch}";
                                        }

                                        return $options;
                                    })
                                    ->getOptionLabelUsing(function ($value): string {
                                        return RoleManagerService::getPrettyName($value);
                                    }),

                                Forms\Components\TextInput::make('guard_name')
                                    ->label('Guardia de Seguridad')
                                    ->default(Utils::getFilamentAuthGuard())
                                    ->nullable()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-o-shield-check')
                                    ->helperText('Sistema de autenticación asociado')
                                    ->disabled()
                                    ->dehydrated(),
                            ]),

                        Forms\Components\Select::make(config('permission.column_names.team_foreign_key'))
                            ->label('Equipo Asociado')
                            ->placeholder('Selecciona un equipo...')
                            ->prefixIcon('heroicon-o-user-group')
                            /** @phpstan-ignore-next-line */
                            ->default([Filament::getTenant()?->id])
                            ->options(fn(): Arrayable => Utils::getTenantModel() ? Utils::getTenantModel()::pluck('name', 'id') : collect())
                            ->hidden(fn(): bool => !(static::shield()->isCentralApp() && Utils::isTenancyEnabled()))
                            ->dehydrated(fn(): bool => !(static::shield()->isCentralApp() && Utils::isTenancyEnabled()))
                            ->helperText('Equipo al que pertenece este rol'),
                    ])
                    ->columnSpan('full'),

                Forms\Components\Section::make('Gestión de Permisos')
                    ->description('Configura los permisos y accesos del rol')
                    ->icon('heroicon-o-key')
                    ->collapsible()
                    ->schema([
                        ShieldSelectAllToggle::make('select_all')
                            ->onIcon('heroicon-s-shield-check')
                            ->offIcon('heroicon-s-shield-exclamation')
                            ->label('Seleccionar Todos los Permisos')
                            ->helperText(fn(): HtmlString => new HtmlString(
                                '<div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Activa/desactiva todos los permisos de una vez
                                </div>'
                            ))
                            ->dehydrated(fn(bool $state): bool => $state)
                            ->columnSpan('full'),

                        static::getShieldFormComponents(),
                    ])
                    ->columnSpan('full'),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre del Rol')
                    ->searchable()
                    ->sortable()
                    ->weight('font-semibold')
                    ->formatStateUsing(fn($state): string => Str::title($state))
                    ->icon('heroicon-o-identification')
                    ->iconColor('primary')
                    ->description(fn($record) => 'Creado ' . $record->created_at->diffForHumans()),

                Tables\Columns\BadgeColumn::make('guard_name')
                    ->label('Guardia')
                    ->color('info')
                    ->icon('heroicon-o-shield-check')
                    ->formatStateUsing(fn($state) => strtoupper($state)),

                Tables\Columns\BadgeColumn::make('team.name')
                    ->label('Equipo')
                    ->default('Global')
                    ->color(fn(mixed $state): string => str($state)->contains('Global') ? 'gray' : 'success')
                    ->icon(fn(mixed $state): string => str($state)->contains('Global') ? 'heroicon-o-globe-alt' : 'heroicon-o-user-group')
                    ->searchable()
                    ->visible(fn(): bool => static::shield()->isCentralApp() && Utils::isTenancyEnabled()),

                Tables\Columns\BadgeColumn::make('permissions_count')
                    ->label('Permisos')
                    ->counts('permissions')
                    ->color(fn($state): string => match (true) {
                        $state === 0 => 'danger',
                        $state <= 5 => 'warning',
                        $state <= 15 => 'info',
                        default => 'success'
                    })
                    ->icon('heroicon-o-key')
                    ->suffix(fn($state) => $state === 1 ? ' permiso' : ' permisos'),

                Tables\Columns\TextColumn::make('users_count')
                    ->label('Usuarios')
                    ->counts('users')
                    ->icon('heroicon-o-users')
                    ->iconColor('gray')
                    ->suffix(fn($state) => $state === 1 ? ' usuario' : ' usuarios')
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última Actualización')
                    ->dateTime('d/M/Y H:i')
                    ->sortable()
                    ->icon('heroicon-o-clock')
                    ->iconColor('gray')
                    ->since()
                    ->tooltip(fn($record) => 'Actualizado: ' . $record->updated_at->format('d/m/Y H:i:s')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('guard_name')
                    ->label('Filtrar por Guardia')
                    ->options([
                        'web' => 'Web',
                        'api' => 'API',
                    ]),

                Tables\Filters\Filter::make('permissions_count')
                    ->label('Roles con Permisos')
                    ->query(fn($query) => $query->has('permissions')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->color('info'),
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning'),
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->modalHeading('Eliminar Rol')
                    ->modalDescription('¿Estás seguro de que deseas eliminar este rol? Esta acción no se puede deshacer.')
                    ->modalSubmitActionLabel('Eliminar')
                    ->color('danger'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation()
                        ->modalHeading('Eliminar Roles Seleccionados')
                        ->modalDescription('¿Estás seguro de que deseas eliminar los roles seleccionados?')
                        ->modalSubmitActionLabel('Eliminar Todos'),
                ]),
            ])
            ->defaultSort('updated_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->poll('30s')
            ->deferLoading()
            ->emptyStateHeading('No hay roles registrados')
            ->emptyStateDescription('Comienza creando tu primer rol de usuario.')
            ->emptyStateIcon('heroicon-o-identification')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->label('Crear Primer Rol'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'view' => Pages\ViewRole::route('/{record}'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function getCluster(): ?string
    {
        return Utils::getResourceCluster() ?? static::$cluster;
    }

    public static function getModel(): string
    {
        return Utils::getRoleModel();
    }

    public static function getModelLabel(): string
    {
        return __('filament-shield::filament-shield.resource.label.role');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-shield::filament-shield.resource.label.roles');
    }

    public static function shouldRegisterNavigation(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        /** @phpstan-ignore-next-line */
        return $user?->hasRole('super_admin') || $user?->can('view_any_role');
    }

    public static function canViewAny(): bool
    {
        return Auth::check() && Gate::allows('view_any_role');
    }

    public static function canView(Model $record): bool
    {
        return Auth::check() && Gate::allows('view_role', $record);
    }

    public static function canCreate(): bool
    {
        return Auth::check() && Gate::allows('create_role');
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::check() && Gate::allows('update_role', $record);
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::check() && Gate::allows('delete_role', $record);
    }

    public static function canDeleteAny(): bool
    {
        return Auth::check() && Gate::allows('delete_any_role');
    }

    public static function getNavigationGroup(): ?string
    {
        return Utils::isResourceNavigationGroupEnabled()
            ? 'Gestión de Usuarios'
            : 'Administración';
    }

    public static function getNavigationLabel(): string
    {
        return 'Roles y Permisos';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-identification';
    }

    public static function getNavigationSort(): ?int
    {
        return Utils::getResourceNavigationSort();
    }

    public static function getSlug(): string
    {
        return Utils::getResourceSlug();
    }

    public static function getNavigationBadge(): ?string
    {
        return Utils::isResourceNavigationBadgeEnabled()
            ? strval(static::getEloquentQuery()->count())
            : null;
    }

    public static function isScopedToTenant(): bool
    {
        return Utils::isScopedToTenant();
    }

    public static function canGloballySearch(): bool
    {
        return Utils::isResourceGloballySearchable() && count(static::getGloballySearchableAttributes()) && static::canViewAny();
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return "Rol: {$record->name}";
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Permisos' => $record->permissions_count ?? $record->permissions()->count(),
            'Usuarios' => $record->users_count ?? $record->users()->count(),
            'Actualizado' => $record->updated_at->diffForHumans(),
        ];
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getEloquentQuery()->count();

        return match (true) {
            $count === 0 => 'primary',
            $count <= 3 => 'primary',
            $count <= 10 => 'primary',
            default => 'primary'
        };
    }
}
