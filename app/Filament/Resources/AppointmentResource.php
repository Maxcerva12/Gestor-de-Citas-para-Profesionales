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
use App\Models\Schedule;
use Filament\Support\Enums\FontWeight;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Filament\Support\Enums\MaxWidth;
use App\Models\Price;


class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    // Navigation and labels
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Citas';
    protected static ?string $navigationGroup = 'Gestión de Citas';
    protected static ?string $label = 'Cita';
    protected static ?string $pluralLabel = 'Citas';
    protected static ?string $recordTitleAttribute = 'id';

    // Navigation sorting
    protected static ?int $navigationSort = 0;

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

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('user_id', Auth::id())->where('status', 'pending')->count() ?: null;
    }

    /**
     * Restringe las citas al profesional autenticado.
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

    public static function getGloballySearchableAttributes(): array
    {
        return ['client.name', 'notes', 'status'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->default(Auth::id()),

                Forms\Components\Section::make('Detalles de la Cita')
                    ->description('Información principal de la cita')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('client_id')
                                    ->label('Cliente')
                                    ->relationship('client', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nombre')
                                            ->required(),
                                        Forms\Components\TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->required(),
                                        Forms\Components\TextInput::make('phone')
                                            ->label('Teléfono')
                                            ->tel(),
                                    ])
                                    ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                                        return $action
                                            ->modalHeading('Crear nuevo cliente')
                                            ->modalWidth(MaxWidth::ExtraLarge);
                                    })
                                    ->required(),

                                Forms\Components\Select::make('schedule_id')
                                    ->label('Horario')
                                    ->relationship('schedule', 'date', function (Builder $query) {
                                        return $query->where('user_id', Auth::id());
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            // Buscar el horario seleccionado
                                            $schedule = Schedule::find($state);
                                            if ($schedule) {
                                                // Crear objetos DateTime combinando la fecha con las horas
                                                $dateObj = new \DateTime($schedule->date);

                                                // Para start_time
                                                $startTimeObj = new \DateTime($schedule->start_time);
                                                $combinedStartTime = (clone $dateObj)
                                                    ->setTime(
                                                        (int) $startTimeObj->format('H'),
                                                        (int) $startTimeObj->format('i'),
                                                        0
                                                    );

                                                // Para end_time
                                                $endTimeObj = new \DateTime($schedule->end_time);
                                                $combinedEndTime = (clone $dateObj)
                                                    ->setTime(
                                                        (int) $endTimeObj->format('H'),
                                                        (int) $endTimeObj->format('i'),
                                                        0
                                                    );

                                                // Establecer los valores combinados
                                                $set('start_time', $combinedStartTime->format('Y-m-d H:i:s'));
                                                $set('end_time', $combinedEndTime->format('Y-m-d H:i:s'));
                                            }
                                        } else {
                                            // Limpiar los campos si no se selecciona horario
                                            $set('start_time', null);
                                            $set('end_time', null);
                                        }
                                    }),
                            ]),

                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\DateTimePicker::make('start_time')
                                    ->label('Hora de Inicio')
                                    ->seconds(false)
                                    ->minutesStep(15)
                                    ->displayFormat('d/m/Y H:i')
                                    ->timezone('America/Bogota')
                                    ->native(false)
                                    ->readonly()
                                    ->required(),

                                Forms\Components\DateTimePicker::make('end_time')
                                    ->label('Hora de Fin')
                                    ->seconds(false)
                                    ->minutesStep(15)
                                    ->displayFormat('d/m/Y H:i')
                                    ->timezone('America/Bogota')
                                    ->after('start_time')
                                    ->native(false)
                                    ->readonly()
                                    ->required(),
                            ]),
                    ]),

                Forms\Components\Tabs::make('Tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Estado y Detalles')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
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
                                            ->required()
                                            ->reactive(),

                                        Forms\Components\Select::make('payment_status')
                                            ->label('Estado del Pago')
                                            ->options([
                                                'pending' => 'Pendiente',
                                                'paid' => 'Pagado',
                                                'failed' => 'Fallido',
                                                'cancelled' => 'Cancelado',
                                            ])
                                            ->default('pending')
                                            ->native(false)
                                            ->required(),
                                    ]),

                                Forms\Components\RichEditor::make('notes')
                                    ->label('Notas')
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'underline',
                                        'bulletList',
                                        'orderedList',
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Facturación')
                            ->icon('heroicon-o-currency-euro')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('price_id')
                                            ->label('Tarifa')
                                            ->relationship('price', 'name')
                                            ->preload()
                                            ->searchable()
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                if ($state) {
                                                    $price = Price::find($state);
                                                    if ($price) {
                                                        $set('amount', $price->amount);
                                                    }
                                                } else {
                                                    $set('amount', null);
                                                }
                                            }),

                                        Forms\Components\TextInput::make('amount')
                                            ->label('Precio Fijo')
                                            ->numeric()
                                            ->prefix('€')
                                            ->disabled()
                                            ->dehydrated(true),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Integración Google')
                            ->icon('heroicon-o-calendar-days')
                            ->schema([
                                Forms\Components\Placeholder::make('google_status')
                                    ->label('Estado de Google Calendar')
                                    ->content(function (Forms\Get $get, ?Model $record) {
                                        if (!$record)
                                            return 'Guarde la cita primero para sincronizar con Google Calendar';

                                        if (!Auth::user()->google_token) {
                                            return 'No has conectado tu cuenta de Google Calendar';
                                        }

                                        if ($record->google_event_id) {
                                            return 'Evento sincronizado con Google Calendar';
                                        }

                                        return 'Evento pendiente de sincronización con Google Calendar';
                                    }),

                                Forms\Components\TextInput::make('google_event_id')
                                    ->label('ID de Evento Google')
                                    ->helperText('ID del evento en Google Calendar')
                                    ->disabled()
                                    ->visible(fn(?Model $record) => $record && $record->google_event_id),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('client.avatar_url')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn($record): string => $record->client ?
                        "https://ui-avatars.com/api/?name=" . urlencode($record->client->name) . "&color=FFFFFF&background=4F46E5" :
                        "https://ui-avatars.com/api/?name=?&color=FFFFFF&background=4F46E5")
                    ->size(40),

                Tables\Columns\TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium)
                    ->description(fn(Appointment $record): string => $record->client->email ?? '')
                    ->wrap(),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Fecha y Hora')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->description(fn(Appointment $record): string => $record->end_time ?
                        'Hasta: ' . date('H:i', strtotime($record->end_time)) : ''),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
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

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Pago')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'Pendiente',
                        'paid' => 'Pagado',
                        'failed' => 'Fallido',
                        'cancelled' => 'Cancelado',
                        default => 'Desconocido',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'failed' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Precio')
                    ->money('EUR')
                    ->sortable()
                    ->toggleable()
                    ->description(fn(Appointment $record): string =>
                        $record->price ? 'Tarifa: ' . $record->price->name : ''),

                Tables\Columns\IconColumn::make('google_event_id')
                    ->label('Google')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->getStateUsing(fn(Appointment $record): bool => (bool) $record->google_event_id)
                    ->toggleable(),
            ])
            ->defaultSort('start_time', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->native(false)
                    ->options([
                        'pending' => 'Pendiente',
                        'confirmed' => 'Confirmada',
                        'canceled' => 'Cancelada',
                        'completed' => 'Completada',
                    ]),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Estado del Pago')
                    ->native(false)
                    ->options([
                        'pending' => 'Pendiente',
                        'paid' => 'Pagado',
                        'failed' => 'Fallido',
                        'cancelled' => 'Cancelado',
                    ]),

                Tables\Filters\SelectFilter::make('client_id')
                    ->label('Cliente')
                    ->native(false)
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Desde')
                            ->native(false)
                            ->placeholder('Desde fecha'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Hasta')
                            ->native(false)
                            ->placeholder('Hasta fecha'),
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),

                    Tables\Actions\EditAction::make(),

                    Tables\Actions\DeleteAction::make(),

                    Action::make('sync_google')
                        ->label('Sincronizar con Google')
                        ->icon('heroicon-o-arrow-path')
                        ->color('gray')
                        ->action(function (Appointment $record) {
                            // Lógica para sincronizar con Google Calendar
                            Notification::make()
                                ->title('Sincronización iniciada')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->visible(
                            fn(Appointment $record) =>
                            Auth::user()->google_token &&
                            !$record->google_event_id
                        ),

                    Action::make('pay')
                        ->label('Procesar Pago')
                        ->icon('heroicon-o-credit-card')
                        ->color('success')
                        ->url(fn(Appointment $record) => route('payment.checkout', $record))
                        ->visible(
                            fn(Appointment $record) =>
                            $record->payment_status === 'pending' &&
                            $record->price_id !== null
                        )
                        ->openUrlInNewTab(),
                ])
                    ->tooltip('Acciones')
                    ->icon('heroicon-m-ellipsis-vertical'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('update_status')
                        ->label('Actualizar Estado')
                        ->icon('heroicon-o-check-circle')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('Estado')
                                ->options([
                                    'pending' => 'Pendiente',
                                    'confirmed' => 'Confirmada',
                                    'canceled' => 'Cancelada',
                                    'completed' => 'Completada',
                                ])
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each(function (Appointment $record) use ($data) {
                                $record->update(['status' => $data['status']]);
                            });

                            Notification::make()
                                ->title('Estado actualizado con éxito')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('exportToCsv')
                        ->label('Exportar seleccionados')
                        ->icon('heroicon-o-document-arrow-down')
                        ->action(function (Collection $records) {
                            // Create CSV file in memory
                            $csv = fopen('php://temp', 'r+');

                            // Add CSV headers
                            fputcsv($csv, [
                                'ID',
                                'Cliente',
                                'Email',
                                'Teléfono',
                                'Fecha',
                                'Hora Inicio',
                                'Hora Fin',
                                'Estado',
                                'Estado de Pago',
                                'Precio',
                                'Notas'
                            ]);

                            // Add appointment data rows
                            foreach ($records as $record) {
                                fputcsv($csv, [
                                    $record->id,
                                    $record->client?->name ?? 'N/A',
                                    $record->client?->email ?? 'N/A',
                                    $record->client?->phone ?? 'N/A',
                                    $record->start_time ? date('d/m/Y', strtotime($record->start_time)) : 'N/A',
                                    $record->start_time ? date('H:i', strtotime($record->start_time)) : 'N/A',
                                    $record->end_time ? date('H:i', strtotime($record->end_time)) : 'N/A',
                                    match ($record->status) {
                                        'pending' => 'Pendiente',
                                        'confirmed' => 'Confirmada',
                                        'canceled' => 'Cancelada',
                                        'completed' => 'Completada',
                                        default => 'Desconocido',
                                    },
                                    match ($record->payment_status) {
                                        'pending' => 'Pendiente',
                                        'paid' => 'Pagado',
                                        'failed' => 'Fallido',
                                        'cancelled' => 'Cancelado',
                                        default => 'Desconocido',
                                    },
                                    $record->price ? $record->price->amount . ' €' : 'N/A',
                                    strip_tags($record->notes ?? '')
                                ]);
                            }

                            // Reset pointer to beginning of file
                            rewind($csv);

                            // Get content
                            $content = stream_get_contents($csv);
                            fclose($csv);

                            // Generate unique filename
                            $filename = 'citas_exportadas_' . date('Y-m-d_His') . '.csv';

                            // Return as a downloadable file
                            return response()->streamDownload(function () use ($content) {
                                echo $content;
                            }, $filename, [
                                'Content-Type' => 'text/csv',
                                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                            ]);
                        }),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Crear Cita'),
            ])
            ->emptyStateDescription('Comienza creando una nueva cita.')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->headerActions([
                Action::make('connectGoogle')
                    ->label('Conectar Google Calendar')
                    ->icon('heroicon-o-calendar')
                    ->url(route('google.auth'))
                    ->color('gray')
                    ->visible(fn() => !Auth::user()->google_token),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Detalles de la Cita')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('client.name')
                                        ->label('Cliente')
                                        ->weight(FontWeight::Bold),
                                    Infolists\Components\TextEntry::make('client.email')
                                        ->label('Email')
                                        ->icon('heroicon-m-envelope')
                                        ->iconColor('primary'),
                                    Infolists\Components\TextEntry::make('client.phone')
                                        ->label('Teléfono')
                                        ->icon('heroicon-m-phone')
                                        ->iconColor('primary'),
                                ]),

                                Infolists\Components\ImageEntry::make('client.avatar_url')
                                    ->label('Avatar')
                                    ->circular()
                                    ->defaultImageUrl(fn($record): string => $record->client ?
                                        "https://ui-avatars.com/api/?name=" . urlencode($record->client->name) . "&color=FFFFFF&background=4F46E5" :
                                        "https://ui-avatars.com/api/?name=?&color=FFFFFF&background=4F46E5")
                                    ->height(100)
                                    ->alignRight(),
                            ]),
                    ]),

                Infolists\Components\Section::make('Horario')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('start_time')
                                    ->label('Fecha y Hora de Inicio')
                                    ->dateTime('d/m/Y H:i')
                                    ->icon('heroicon-m-calendar'),
                                Infolists\Components\TextEntry::make('end_time')
                                    ->label('Fecha y Hora de Fin')
                                    ->dateTime('d/m/Y H:i')
                                    ->icon('heroicon-m-clock'),
                            ]),
                    ]),

                Infolists\Components\Tabs::make('Tabs')
                    ->tabs([
                        Infolists\Components\Tabs\Tab::make('Estado')
                            ->schema([
                                Infolists\Components\Grid::make(2)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('status')
                                            ->label('Estado de la Cita')
                                            ->badge()
                                            ->formatStateUsing(fn(string $state): string => match ($state) {
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

                                        Infolists\Components\TextEntry::make('payment_status')
                                            ->label('Estado del Pago')
                                            ->badge()
                                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                                'pending' => 'Pendiente',
                                                'paid' => 'Pagado',
                                                'failed' => 'Fallido',
                                                'cancelled' => 'Cancelado',
                                                default => 'Desconocido',
                                            })
                                            ->color(fn(string $state): string => match ($state) {
                                                'pending' => 'warning',
                                                'paid' => 'success',
                                                'failed' => 'danger',
                                                'cancelled' => 'gray',
                                                default => 'gray',
                                            }),
                                    ]),

                                Infolists\Components\TextEntry::make('notes')
                                    ->label('Notas')
                                    ->markdown()
                                    ->columnSpanFull(),
                            ]),

                        Infolists\Components\Tabs\Tab::make('Facturación')
                            ->schema([
                                Infolists\Components\Grid::make(2)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('price.name')
                                            ->label('Tarifa'),

                                        Infolists\Components\TextEntry::make('amount')
                                            ->label('Precio')
                                            ->money('EUR')
                                            ->weight(FontWeight::Bold),
                                    ]),

                                Infolists\Components\Grid::make(1)
                                    ->schema([
                                        // Referencias de Stripe eliminadas
                                    ]),
                            ]),

                        Infolists\Components\Tabs\Tab::make('Google Calendar')
                            ->schema([
                                Infolists\Components\TextEntry::make('google_event_id')
                                    ->label('ID de Evento Google')
                                    ->copyable()
                                    ->icon('heroicon-m-calendar')
                                    ->visible(fn(Appointment $record) => (bool) $record->google_event_id),

                                Infolists\Components\TextEntry::make('google_sync_status')
                                    ->label('Estado de Sincronización')
                                    ->badge()
                                    ->state(fn(Appointment $record) => $record->google_event_id ? 'Sincronizado' : 'No sincronizado')
                                    ->color(fn(Appointment $record) => $record->google_event_id ? 'success' : 'gray'),
                            ]),
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
