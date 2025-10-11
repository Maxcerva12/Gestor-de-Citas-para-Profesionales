<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Collection;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    // Navigation and labels
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationLabel = 'Servicios';
    protected static ?string $navigationGroup = 'Gestión de Servicios';
    protected static ?string $label = 'Servicio';
    protected static ?string $pluralLabel = 'Servicios';
    protected static ?string $recordTitleAttribute = 'name';

    // Navigation sorting
    protected static ?int $navigationSort = 1;

    // Permission checks
    public static function canViewAny(): bool
    {
        return Auth::check();
    }

    public static function canView(Model $record): bool
    {
        return Auth::check() && (Auth::user()->hasRole('super_admin') || $record->user_id === Auth::id());
    }

    public static function canCreate(): bool
    {
        return Auth::check();
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::check() && (Auth::user()->hasRole('super_admin') || $record->user_id === Auth::id());
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::check() && (Auth::user()->hasRole('super_admin') || $record->user_id === Auth::id());
    }

    public static function canDeleteAny(): bool
    {
        return Auth::check();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check();
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('user_id', Auth::id())->where('is_active', true)->count() ?: null;
    }

    /**
     * Restringe los servicios al profesional autenticado (excepto super_admin)
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Si el usuario es superAdmin, mostrar todos los servicios
        // Si no, mostrar solo los servicios del usuario autenticado
        if (!Auth::user() || !Auth::user()->hasRole('super_admin')) {
            $query->where('user_id', Auth::id());
        }

        return $query;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'description'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->default(Auth::id()),

                Forms\Components\Section::make('Información del Servicio')
                    ->description('Detalles básicos del servicio que ofreces')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del Servicio')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Consulta General, Limpieza Dental, etc.'),

                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->rows(3)
                            ->placeholder('Describe brevemente en qué consiste este servicio')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Configuración de Precios y Tiempo')
                    ->description('Establece el precio y duración estimada del servicio')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->label('Precio (COP)')
                                    ->required()
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->step(1000)
                                    ->placeholder('50000')
                                    ->helperText('Precio en pesos colombianos'),

                                Forms\Components\TextInput::make('duration')
                                    ->label('Duración (minutos)')
                                    ->required()
                                    ->numeric()
                                    ->default(30)
                                    ->minValue(15)
                                    ->maxValue(480)
                                    ->step(15)
                                    ->suffix('min')
                                    ->helperText('Tiempo estimado del servicio'),
                            ]),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Servicio Activo')
                            ->helperText('Solo los servicios activos aparecerán disponibles para las citas')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Servicio')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium)
                    ->description(fn(Service $record): string => $record->description ?? ''),

                Tables\Columns\TextColumn::make('price')
                    ->label('Precio')
                    ->money('COP')
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->color('success'),

                Tables\Columns\TextColumn::make('duration')
                    ->label('Duración')
                    ->formatStateUsing(fn(int $state): string => $state . ' min')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('appointments_count')
                    ->label('Citas')
                    ->counts('appointments')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Profesional')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: !Auth::user()?->hasRole('super_admin')),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Estado')
                    ->placeholder('Todos los servicios')
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos')
                    ->native(false),

                Tables\Filters\SelectFilter::make('price')
                    ->label('Rango de Precio')
                    ->options([
                        'low' => 'Menos de $50.000',
                        'medium' => '$50.000 - $100.000',
                        'high' => 'Más de $100.000',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] === 'low',
                            fn(Builder $query): Builder => $query->where('price', '<', 50000),
                        )->when(
                                $data['value'] === 'medium',
                                fn(Builder $query): Builder => $query->whereBetween('price', [50000, 100000]),
                            )->when(
                                $data['value'] === 'high',
                                fn(Builder $query): Builder => $query->where('price', '>', 100000),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                    ->tooltip('Acciones')
                    ->icon('heroicon-m-ellipsis-vertical'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('toggle_active')
                        ->label('Cambiar Estado')
                        ->icon('heroicon-o-arrow-path')
                        ->action(function (Collection $records) {
                            $records->each(function (Service $record) {
                                $record->update(['is_active' => !$record->is_active]);
                            });
                        }),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Crear Primer Servicio'),
            ])
            ->emptyStateDescription('Comienza creando tu primer servicio profesional.')
            ->striped()
            ->paginated([10, 25, 50]);
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
