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

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Horarios';

    /**
     * Restringe el resource a los horarios del usuario autenticado.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
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
                    ->minDate(now())
                    ->reactive(),
                Forms\Components\TimePicker::make('start_time')
                    ->label('Hora de Inicio')
                    ->required()
                    ->seconds(false)
                    ->reactive(),
                Forms\Components\TimePicker::make('end_time')
                    ->label('Hora de Fin')
                    ->required()
                    ->seconds(false)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        // Validar que la hora de fin sea posterior a la de inicio
                        $startTime = $get('start_time');
                        if ($startTime && $state && $state <= $startTime) {
                            $set('end_time', null);
                        }
                    }),
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
