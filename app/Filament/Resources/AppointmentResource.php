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
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        $query = static::getModel()::where('status', 'pending');

        // Si es super_admin, contar todas las citas pendientes
        if ($user->hasRole('super_admin')) {
            return $query->count() ?: null;
        }

        // Para usuarios con view_all_revenue o cualquier otro, mostrar solo sus citas pendientes
        return $query->where('user_id', $user->id)->count() ?: null;
    }

    /**
     * Restringe las citas según el rol y permisos del usuario.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = Auth::user();

        // Si no hay usuario autenticado, no mostrar nada
        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        // Si el usuario es superAdmin, mostrar todas las citas
        if ($user->hasRole('super_admin')) {
            return $query; // No aplicar filtros
        }

        // Si el usuario tiene el permiso view_all_revenue, mostrar solo sus citas
        if ($user->can('view_all_revenue')) {
            $query->where('user_id', $user->id);
            return $query;
        }

        // Para cualquier otro usuario, mostrar solo sus citas
        $query->where('user_id', $user->id);

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
                                        return $query->where('user_id', Auth::id())
                                            ->reallyAvailable()
                                            ->orderBy('date', 'asc')
                                            ->orderBy('start_time', 'asc');
                                    })
                                    ->getOptionLabelFromRecordUsing(function ($record) {
                                        return date('d/m/Y', strtotime($record->date)) .
                                            ' - ' .
                                            date('H:i', strtotime($record->start_time)) .
                                            ' a ' .
                                            date('H:i', strtotime($record->end_time));
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->helperText('Solo se muestran horarios disponibles (no ocupados y no expirados)')
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

                Forms\Components\Section::make('Servicio y Pago')
                    ->description('Selecciona el servicio y método de pago')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('service_id')
                                    ->label('Servicio')
                                    ->relationship('service', 'name', function (Builder $query) {
                                        return $query->where('user_id', Auth::id())->where('is_active', true);
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $service = \App\Models\Service::find($state);
                                            if ($service) {
                                                $set('service_price', $service->price);
                                            }
                                        } else {
                                            $set('service_price', null);
                                        }
                                    })
                                    ->helperText('Selecciona el servicio profesional a realizar'),

                                Forms\Components\TextInput::make('service_price')
                                    ->label('Precio del Servicio (COP)')
                                    ->numeric()
                                    ->prefix('$')
                                    ->readonly()
                                    ->helperText('Precio se actualiza automáticamente al seleccionar servicio'),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('payment_method')
                                    ->label('Método de Pago')
                                    ->options([
                                        'efectivo' => 'Efectivo',
                                        'transferencia' => 'Transferencia Bancaria',
                                        'tarjeta_debito' => 'Tarjeta de Débito (Wompi)',
                                    ])
                                    ->native(false)
                                    ->helperText('Cómo pagará el cliente el servicio'),

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
                                    ->required()
                                    ->helperText('Estado actual del pago'),

                                Forms\Components\Placeholder::make('invoice_info')
                                    ->label('Facturación')
                                    ->content(function ($record) {
                                        if (!$record)
                                            return 'La factura se generará automáticamente';

                                        $invoiceCount = $record->invoices()->count();
                                        if ($invoiceCount > 0) {
                                            return "✅ {$invoiceCount} factura(s) generada(s)";
                                        }

                                        return 'Pendiente de facturación';
                                    }),
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
                                            ->label('Estado de la Cita')
                                            ->options([
                                                'pending' => 'Pendiente',
                                                'confirmed' => 'Confirmada',
                                                'canceled' => 'Cancelada',
                                                'completed' => 'Completada',
                                            ])
                                            ->default('pending')
                                            ->native(false)
                                            ->required()
                                            ->columnSpanFull(),
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

                Tables\Columns\TextColumn::make('service.name')
                    ->label('Servicio')
                    ->searchable()
                    ->sortable()
                    ->description(
                        fn(Appointment $record): string =>
                        $record->service_price ? '$' . number_format((float) $record->service_price, 2, ',', '.') : 'Sin precio'
                    )
                    ->placeholder('Sin servicio')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('payment_method')
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
                    ])
                    ->placeholder('Todos los estados'),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Estado del Pago')
                    ->native(false)
                    ->options([
                        'pending' => 'Pendiente',
                        'paid' => 'Pagado',
                        'failed' => 'Fallido',
                        'cancelled' => 'Cancelado',
                    ])
                    ->placeholder('Todos los pagos'),

                Tables\Filters\SelectFilter::make('client_id')
                    ->label('Cliente')
                    ->native(false)
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload(fn() => \App\Models\Client::count() < 100) // Preload solo si hay <100 clientes
                    ->getSearchResultsUsing(function (string $search) {
                        return \App\Models\Client::where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->limit(50)
                            ->pluck('name', 'id');
                    })
                    ->placeholder('Buscar cliente...'),

                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Desde')
                            ->native(false)
                            ->placeholder('dd/mm/yyyy')
                            ->maxDate(fn(callable $get) => $get('until')), // No permitir fecha mayor a "hasta"
                        Forms\Components\DatePicker::make('until')
                            ->label('Hasta')
                            ->native(false)
                            ->placeholder('dd/mm/yyyy')
                            ->minDate(fn(callable $get) => $get('from')), // No permitir fecha menor a "desde"
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn($q, $date) => $q->whereRaw('DATE(start_time) >= ?', [$date])
                            )
                            ->when(
                                $data['until'],
                                fn($q, $date) => $q->whereRaw('DATE(start_time) <= ?', [$date])
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['from'] ?? null) {
                            $indicators[] = 'Desde: ' . \Carbon\Carbon::parse($data['from'])->format('d/m/Y');
                        }

                        if ($data['until'] ?? null) {
                            $indicators[] = 'Hasta: ' . \Carbon\Carbon::parse($data['until'])->format('d/m/Y');
                        }

                        return $indicators;
                    })
                // Ocupa 2 columnas para mejor visualización
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
                            try {
                                // Verificar que el profesional tiene token de Google
                                if (!$record->user || !$record->user->google_token) {
                                    Notification::make()
                                        ->title('Error')
                                        ->body('El profesional no tiene configurado Google Calendar.')
                                        ->danger()
                                        ->send();
                                    return;
                                }

                                // Usar el servicio de Google Calendar
                                $googleService = app(\App\Services\GoogleCalendarService::class);

                                // Temporalmente autenticar como el profesional
                                $originalUser = Auth::user();
                                Auth::login($record->user);

                                $eventId = $googleService->createEvent($record);
                                $record->google_event_id = $eventId;
                                $record->save();

                                // Restaurar usuario original
                                if ($originalUser) {
                                    Auth::login($originalUser);
                                }

                                Notification::make()
                                    ->title('Sincronizado')
                                    ->body('Cita sincronizada con Google Calendar correctamente.')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Error de sincronización')
                                    ->body('Error: ' . $e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Sincronizar con Google Calendar')
                        ->modalDescription('¿Estás seguro de que deseas sincronizar esta cita con Google Calendar?')
                        ->visible(
                            fn(Appointment $record) =>
                            $record->user &&
                            $record->user->google_token &&
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
