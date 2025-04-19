<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduleResource\Pages;
use App\Models\Schedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

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

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->default(Auth::id()), // Asigna el ID del profesional autenticado
                Forms\Components\DatePicker::make('date')
                    ->label('Fecha')
                    ->required()
                    ->minDate(now()->startOfDay()) // Permitir la fecha actual
                    ->native(false)
                    ->format('Y-m-d') // Formato de almacenamiento
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $dayOfWeek = date('N', strtotime($state));
                            if ($dayOfWeek > 5) {
                                $set('date', null);
                                \Filament\Notifications\Notification::make()
                                    ->title('Fecha no válida')
                                    ->body('Solo se pueden crear citas de lunes a viernes.')
                                    ->danger()
                                    ->send();
                            }
                        }
                    }),
                Forms\Components\TimePicker::make('start_time')
                    ->label('Hora de Inicio')
                    ->required()
                    ->native(false)
                    ->seconds(false)
                    ->reactive(),
                Forms\Components\TimePicker::make('end_time')
                    ->label('Hora de Fin')
                    ->required()
                    ->seconds(false)
                    ->native(false)
                    ->reactive(),
                Forms\Components\Toggle::make('is_available')
                    ->label('Disponible')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Fecha')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Hora de Inicio')
                    ->time('H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_time')
                    ->label('Hora de Fin')
                    ->time('H:i')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_available')
                    ->label('Disponible')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                // Aquí puedes agregar filtros si lo deseas
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
}
