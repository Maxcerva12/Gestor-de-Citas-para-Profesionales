<?php

namespace App\Filament\Client\Widgets;

use App\Models\Appointment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class NextAppointmentWidget extends BaseWidget
{
    protected static ?string $heading = 'Mis Próximas Citas';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Appointment::query()
                    ->where('client_id', Auth::guard('client')->id())
                    ->where('start_time', '>=', now())
                    ->where('status', '!=', 'canceled')
                    ->with(['service:id,name', 'user:id,name'])
                    ->orderBy('start_time', 'asc')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Hora')
                    ->time('H:i')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('service.name')
                    ->label('Servicio')
                    ->wrap()
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Profesional')
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'primary' => 'in_progress',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'Pendiente',
                        'confirmed' => 'Confirmada',
                        'in_progress' => 'En Curso',
                        default => $state,
                    }),
            ])
            ->emptyStateHeading('No hay citas programadas')
            ->emptyStateDescription('Cuando programes nuevas citas, aparecerán aquí.')
            ->emptyStateIcon('heroicon-o-calendar-days')
            ->paginated(false);
    }
}