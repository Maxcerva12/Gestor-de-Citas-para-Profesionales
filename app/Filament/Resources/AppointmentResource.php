<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppointmentResource\Pages;
use App\Models\Appointment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Filament\Tables\Actions\Action;
use  APP\Models\Price;
class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Citas';
    protected static ?string $navigationGroup = 'Gestión de Citas';
    protected static ?string $label = 'Cita';
    protected static ?string $pluralLabel = 'Citas';

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
        return Auth::check() && Gate::allows('view_any_appointment');
    }

    public static function canView(Model $record): bool
    {
        return Auth::check() && Gate::allows('view_appointment', $record);
    }

    public static function canCreate(): bool
    {
        return Auth::check() && Gate::allows('create_appointment');
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::check() && Gate::allows('update_appointment', $record);
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::check() && Gate::allows('delete_appointment', $record);
    }

    public static function canDeleteAny(): bool
    {
        return Auth::check() && Gate::allows('delete_any_appointment');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Gate::allows('view_any_appointment');
    }

    /**
     * Restringe las citas al profesional autenticado.
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
                    ->default(Auth::id()),
                Forms\Components\Select::make('client_id')
                    ->label('Cliente')
                    ->relationship('client', 'name')
                    ->native(false)
                    ->required(),
                Forms\Components\Select::make('schedule_id')
                    ->label('Horario')
                    ->relationship('schedule', 'date')
                    ->native(false)
                    ->required(),
                Forms\Components\DateTimePicker::make('start_time')
                    ->label('Hora de Inicio')
                    ->native(false)
                    ->required(),
                Forms\Components\DateTimePicker::make('end_time')
                    ->label('Hora de Fin')
                    ->native(false)
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'confirmed' => 'Confirmada',
                        'canceled' => 'Cancelada',
                        'completed' => 'Completada',
                    ])
                    ->default('pending')
                    ->native(false)
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->label('Notas')
                    ->nullable(),
                    Forms\Components\Select::make('price_id')
                    ->label('Precio')
                    ->relationship('price', 'name')
                    ->reactive()
                    // ->afterStateUpdated(function ($state, callable $set) {
                    //     if ($state) {
                    //         $price = Price::find($state);
                    //         if ($price && $price->duration) {
                    //             $set('duration', $price->duration);
                    //         }
                    //     }
                    // })
                    // ->required(),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable(),
                Tables\Columns\TextColumn::make('schedule.date')
                    ->label('Fecha')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Inicio')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_time')
                    ->label('Fin')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'canceled' => 'danger',
                        'completed' => 'info',
                    }),
                Tables\Columns\TextColumn::make('price.amount')
                    ->label('Precio')
                    ->money('EUR'),
                    
                    Tables\Columns\TextColumn::make('payment_status')
                    ->label('Estado del Pago')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pendiente',
                        'paid' => 'Pagado',
                        'failed' => 'Fallido',
                        'cancelled' => 'Cancelado',
                        default => 'Desconocido',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'failed' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pendiente',
                        'confirmed' => 'Confirmada',
                        'canceled' => 'Cancelada',
                        'completed' => 'Completada',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('connectGoogle')
                    ->label('Conectar Google Calendar')
                    ->url(route('google.auth'))
                    ->visible(fn() => !Auth::user()->google_token),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                
                Action::make('pay')
                ->label('Pagar')
                ->icon('heroicon-o-credit-card')
                ->color('success')
                ->url(fn (Appointment $record) => route('payment.checkout', $record))
                ->visible(fn (Appointment $record) => 
                    $record->payment_status === 'pending' && 
                    $record->price_id !== null
                )
                ->openUrlInNewTab(),
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
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}
