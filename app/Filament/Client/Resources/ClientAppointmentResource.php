<?php

namespace App\Filament\Client\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Appointment;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Tabs;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use App\Filament\Client\Resources\ClientAppointmentResource\Pages;


class ClientAppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Mis Citas';

    protected static ?string $recordTitleAttribute = 'start_time';

    protected static ?string $navigationGroup = 'Agendamientos de Citas';


    protected static ?string $modelLabel = 'Cita';

    protected static ?string $pluralModelLabel = 'Citas';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('client_id', auth()->id())
            ->where('status', 'pending')
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('user.avatar_url')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn($record): string =>
                        "https://ui-avatars.com/api/?name=" . urlencode($record->user?->name ?? '?') . "&color=FFFFFF&background=4F46E5")
                    ->size(40),

                TextColumn::make('user.name')
                    ->label('Profesional')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium)
                    ->description(fn($record) => $record->user?->profession ?? '')
                    ->wrap(),

                TextColumn::make('start_time')
                    ->label('Fecha y Hora')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->description(fn($record): string => $record->end_time ?
                        'Hasta: ' . date('H:i', strtotime($record->end_time)) : '')
                    ->icon('heroicon-o-calendar')
                    ->iconPosition(IconPosition::Before),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pending' => 'Pendiente',
                        'confirmed' => 'Confirmada',
                        'canceled' => 'Cancelada',
                        'completed' => 'Completada',
                        default => 'Desconocido',
                    })
                    ->color(fn($state) => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'canceled' => 'danger',
                        'completed' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('payment_status')
                    ->label('Pago')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pending' => 'Pendiente',
                        'paid' => 'Pagado',
                        'failed' => 'Fallido',
                        'cancelled' => 'Cancelado',
                        default => 'Desconocido',
                    })
                    ->color(fn($state) => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'failed' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('service_price')
                    ->label('Precio')
                    ->money('COP')
                    ->sortable()
                    ->icon('heroicon-o-currency-dollar')
                    ->iconPosition(IconPosition::Before)
                    ->placeholder('Sin precio'),

                TextColumn::make('service.name')
                    ->label('Servicio')
                    ->searchable()
                    ->wrap()
                    ->toggleable()
                    ->placeholder('Sin servicio'),

                TextColumn::make('payment_method')
                    ->label('Método de Pago')
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'efectivo' => 'Efectivo',
                        'transferencia' => 'Transferencia',
                        'tarjeta_debito' => 'Tarjeta de Débito',
                        default => 'No especificado',
                    })
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'efectivo' => 'success',
                        'transferencia' => 'info',
                        'tarjeta_debito' => 'warning',
                        default => 'gray',
                    })
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'confirmed' => 'Confirmada',
                        'canceled' => 'Cancelada',
                        'completed' => 'Completada',
                    ]),

                SelectFilter::make('payment_status')
                    ->label('Estado del Pago')
                    ->options([
                        'pending' => 'Pendiente',
                        'paid' => 'Pagado',
                        'failed' => 'Fallido',
                        'cancelled' => 'Cancelado',
                    ]),

                Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Desde')
                            ->native(false)
                            ->placeholder('Seleccione fecha'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Hasta')
                            ->native(false)
                            ->placeholder('Seleccione fecha'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('start_time', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('start_time', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Detalles')
                    ->icon('heroicon-o-eye')
                    ->color('primary'),
            ])
            ->defaultSort('start_time', 'desc')
            ->emptyStateIcon('heroicon-o-calendar')
            ->emptyStateHeading('Aún no tienes citas')
            ->emptyStateDescription('No se han encontrado citas programadas. Ponte en contacto con tu profesional para programar una.')
            ->striped()
            ->paginated([10, 25, 50]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Información general de la cita
                Section::make('Información de la Cita')
                    ->icon('heroicon-o-information-circle')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Group::make([
                                    TextEntry::make('start_time')
                                        ->label('Fecha y Hora de Inicio')
                                        ->dateTime('d/m/Y H:i')
                                        ->weight(FontWeight::Bold)
                                        ->icon('heroicon-m-calendar'),

                                    TextEntry::make('end_time')
                                        ->label('Fecha y Hora de Fin')
                                        ->dateTime('d/m/Y H:i')
                                        ->icon('heroicon-m-clock'),
                                ]),

                                Group::make([
                                    TextEntry::make('status')
                                        ->label('Estado')
                                        ->badge()
                                        ->formatStateUsing(fn($state) => match ($state) {
                                            'pending' => 'Pendiente',
                                            'confirmed' => 'Confirmada',
                                            'canceled' => 'Cancelada',
                                            'completed' => 'Completada',
                                            default => 'Desconocido',
                                        })
                                        ->color(fn($state) => match ($state) {
                                            'pending' => 'warning',
                                            'confirmed' => 'success',
                                            'canceled' => 'danger',
                                            'completed' => 'info',
                                            default => 'gray',
                                        }),

                                    TextEntry::make('payment_status')
                                        ->label('Estado del Pago')
                                        ->badge()
                                        ->formatStateUsing(fn($state) => match ($state) {
                                            'pending' => 'Pendiente',
                                            'paid' => 'Pagado',
                                            'failed' => 'Fallido',
                                            'cancelled' => 'Cancelado',
                                            default => 'Desconocido',
                                        })
                                        ->color(fn($state) => match ($state) {
                                            'pending' => 'warning',
                                            'paid' => 'success',
                                            'failed' => 'danger',
                                            'cancelled' => 'gray',
                                            default => 'gray',
                                        }),
                                ]),
                            ]),
                    ]),

                // Información del profesional
                Section::make('Datos del Profesional')
                    ->icon('heroicon-o-user')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Group::make([
                                    TextEntry::make('user.name')
                                        ->label('Nombre')
                                        ->weight(FontWeight::Bold),

                                    TextEntry::make('user.email')
                                        ->label('Email')
                                        ->icon('heroicon-m-envelope')
                                        ->copyable()
                                        ->iconColor('primary'),

                                    TextEntry::make('user.phone')
                                        ->label('Teléfono')
                                        ->icon('heroicon-m-phone')
                                        ->iconColor('primary'),

                                    TextEntry::make('user.profession')
                                        ->label('Profesión')
                                        ->icon('heroicon-m-briefcase'),
                                ]),

                                ImageEntry::make('user.avatar_url')
                                    ->label('Avatar')
                                    ->circular()
                                    ->defaultImageUrl(fn($record): string =>
                                        "https://ui-avatars.com/api/?name=" . urlencode($record->user?->name ?? '?') . "&color=FFFFFF&background=4F46E5")
                                    ->height(100)
                                    ->alignRight(),
                            ]),
                    ]),

                // Pestañas para detalles adicionales
                Tabs::make('Detalles Adicionales')
                    ->tabs([
                        Tabs\Tab::make('Detalles de la Cita')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                TextEntry::make('notes')
                                    ->label('Notas')
                                    ->markdown()
                                    ->hiddenLabel(),
                            ]),

                        Tabs\Tab::make('Información de Pago')
                            ->icon('heroicon-o-currency-euro')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('price.name')
                                            ->label('Tarifa')
                                            ->icon('heroicon-o-tag'),

                                        TextEntry::make('amount')
                                            ->label('Precio')
                                            ->money('EUR')
                                            ->weight(FontWeight::Bold)
                                            ->icon('heroicon-o-currency-euro'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Ubicación')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                TextEntry::make('schedule.location')
                                    ->label('Dirección')
                                    ->icon('heroicon-o-map-pin')
                                    ->hiddenLabel(),
                            ]),
                    ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('client_id', auth()->id());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClientAppointments::route('/'),
            // 'view' => Pages\ViewClientAppointment::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}