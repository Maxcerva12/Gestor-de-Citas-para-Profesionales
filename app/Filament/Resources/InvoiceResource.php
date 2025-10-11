<?php

namespace App\Filament\Resources;

use App\Enums\InvoiceState;
use App\Enums\InvoiceType;
use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Client;
use App\Models\Invoice;
use Brick\Money\Money;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Get;
use Filament\Forms\Set;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Facturas de Servicios';

    protected static ?string $modelLabel = 'Factura de Servicio';

    protected static ?string $pluralModelLabel = 'Facturas de Servicios';

    protected static ?string $navigationGroup = 'Facturación';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información General')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Tipo de Factura')
                            ->options(InvoiceType::class)
                            ->native(false)
                            ->default('invoice')
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('state')
                            ->label('Estado')
                            ->options(InvoiceState::class)
                            ->native(false)
                            ->default('draft')
                            ->required(),

                        Forms\Components\TextInput::make('serial_number')
                            ->label('Número de Serie')
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\Textarea::make('description')
                            ->label('Descripción/Observaciones')
                            ->placeholder('Describa los servicios prestados o agregue observaciones importantes')
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\DatePicker::make('due_at')
                            ->label('Fecha de Vencimiento')
                            ->default(now()->addDays(30))
                            ->native(false)
                            ->required(),

                    ])
                    ->columns(2),

                Forms\Components\Section::make('Paciente')
                    ->schema([
                        Forms\Components\Select::make('buyer_id')
                            ->label('Paciente')
                            ->relationship('client', 'name')
                            ->getOptionLabelFromRecordUsing(
                                fn(Client $record): string =>
                                $record->name . ' ' . ($record->apellido ?? '') . ' - ' . ($record->numero_documento ?? 'Sin documento')
                            )
                            ->searchable(['name', 'apellido', 'numero_documento', 'email'])
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                if ($state) {
                                    // Obtener el cliente seleccionado
                                    $client = Client::find($state);
                                    if ($client) {
                                        // Obtener campos existentes para no sobrescribir campos personalizados
                                        $existingFields = $get('buyer_information.fields') ?? [];

                                        // Preparar campos del cliente (solo los que tienen valor)
                                        $clientFields = array_filter([
                                            'Tipo de Documento' => $client->tipo_documento ?? 'Cédula de Ciudadanía',
                                            'Número de Documento' => $client->numero_documento,
                                            'Género' => $client->genero,
                                            'Fecha de Nacimiento' => $client->fecha_nacimiento ? \Carbon\Carbon::parse($client->fecha_nacimiento)->format('d/m/Y') : null,
                                            'Tipo de Sangre' => $client->tipo_sangre,
                                            'Aseguradora' => $client->aseguradora,
                                        ], function ($value) {
                                            return $value !== null && $value !== '';
                                        });

                                        // Combinar campos: mantener los personalizados y agregar/actualizar los del cliente
                                        $combinedFields = array_merge($clientFields, $existingFields);

                                        // Actualizar el campo buyer_information.fields
                                        $set('buyer_information.fields', $combinedFields);
                                    }
                                } else {
                                    // Si no hay cliente seleccionado, limpiar solo los campos automáticos
                                    $existingFields = $get('buyer_information.fields') ?? [];
                                    $automaticFields = ['Tipo de Documento', 'Número de Documento', 'Género', 'Fecha de Nacimiento', 'Tipo de Sangre', 'Aseguradora'];

                                    $customFields = array_filter($existingFields, function ($key) use ($automaticFields) {
                                        return !in_array($key, $automaticFields);
                                    }, ARRAY_FILTER_USE_KEY);

                                    $set('buyer_information.fields', $customFields);
                                }
                            })

                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombre')
                                    ->required(),
                                Forms\Components\TextInput::make('apellido')
                                    ->label('Apellido')
                                    ->required(),
                                Forms\Components\TextInput::make('email')
                                    ->label('Correo Electrónico')
                                    ->email(),
                                Forms\Components\TextInput::make('phone')
                                    ->label('Teléfono'),
                                Forms\Components\Select::make('tipo_documento')
                                    ->label('Tipo de Documento')
                                    ->options([
                                        'CC' => 'Cédula de Ciudadanía',
                                        'CE' => 'Cédula de Extranjería',
                                        'TI' => 'Tarjeta de Identidad',
                                        'PP' => 'Pasaporte',
                                    ])
                                    ->default('CC')
                                    ->required(),
                                Forms\Components\TextInput::make('numero_documento')
                                    ->label('Número de Documento')
                                    ->required(),
                                Forms\Components\Textarea::make('address')
                                    ->label('Dirección'),
                            ])
                            ->required(),

                        Forms\Components\KeyValue::make('buyer_information.fields')
                            ->label('Información Adicional del Paciente')
                            ->helperText('Los campos del paciente se llenan automáticamente. Puedes agregar información adicional usando el botón "Agregar elemento".')
                            ->keyLabel('Campo')
                            ->valueLabel('Valor')
                            ->addable(true)
                            ->deletable(true)
                            ->reorderable(false)
                            ->default([])
                            ->afterStateHydrated(function ($component, $state) {
                                // Solo limpiar valores problemáticos específicos, mantener el resto
                                if (is_array($state)) {
                                    $cleanedState = array_filter($state, function ($value, $key) {
                                        // Eliminar solo campos con valores problemáticos específicos
                                        if ($key === 'Documento' && ($value === null || $value === '')) {
                                            return false;
                                        }
                                        if ($key === 'Tipo de Cliente' && $value === 'Natural') {
                                            return false;
                                        }
                                        return true;
                                    }, ARRAY_FILTER_USE_BOTH);

                                    $component->state($cleanedState);
                                }
                            }),
                    ]),

                Forms\Components\Section::make('Información de la Clínica')
                    ->schema([
                        Forms\Components\KeyValue::make('seller_information.fields')
                            ->label('Campos Personalizados')
                            ->keyLabel('Campo')
                            ->valueLabel('Valor')
                            ->default([
                                'Régimen' => 'Común',
                                'Actividad Económica' => 'Servicios Odontológicos',
                                'Registro Sanitario' => 'HABILITACIÓN ODONTOLÓGICA',
                            ]),
                    ]),

                Forms\Components\Section::make('Servicios Prestados')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship('items')
                            ->label('Items de la Factura')
                            ->schema([
                                Forms\Components\TextInput::make('label')
                                    ->label('Servicio')
                                    ->placeholder('Ej: Limpieza dental, Ortodoncia, etc.')
                                    ->required()
                                    ->columnSpan(2),

                                Forms\Components\Textarea::make('description')
                                    ->label('Descripción del Servicio')
                                    ->placeholder('Descripción detallada del servicio prestado')
                                    ->rows(2)
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('unit_price')
                                    ->label('Precio Unitario')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        if (!$state)
                                            return;

                                        // Obtener valor actual sin caché
                                        $taxRateSetting = \App\Models\InvoiceSettings::where('key', 'tax_rate')->first();
                                        $taxRate = $taxRateSetting ? (float) $taxRateSetting->value : 19;
                                        $set('tax_percentage', $taxRate);
                                    }),

                                Forms\Components\TextInput::make('quantity')
                                    ->label('Cantidad')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        if (!$state)
                                            return;

                                        // Obtener valor actual sin caché
                                        $taxRateSetting = \App\Models\InvoiceSettings::where('key', 'tax_rate')->first();
                                        $taxRate = $taxRateSetting ? (float) $taxRateSetting->value : 19;
                                        $set('tax_percentage', $taxRate);
                                    }),

                                Forms\Components\TextInput::make('tax_percentage')
                                    ->label('IVA (%)')
                                    ->numeric()
                                    ->default(function () {
                                        // Obtener valor actual sin caché
                                        $taxRateSetting = \App\Models\InvoiceSettings::where('key', 'tax_rate')->first();
                                        return $taxRateSetting ? (float) $taxRateSetting->value : 19;
                                    })
                                    ->suffix('%')
                                    ->disabled()
                                    ->dehydrated(),
                            ])
                            ->columns(3)
                            ->defaultItems(1)
                            ->addActionLabel('Agregar Servicio')
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => $state['label'] ?? 'Nuevo Servicio')
                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                $data['currency'] = 'COP';
                                // Asegurar que tax_percentage sea un valor entero (19 no 0.19)
                                if (isset($data['tax_percentage'])) {
                                    $taxRate = (float) $data['tax_percentage'];
                                    if ($taxRate < 1) {
                                        // Si es decimal (0.19), convertir a porcentaje (19)
                                        $data['tax_percentage'] = $taxRate * 100;
                                    }
                                } else {
                                    $data['tax_percentage'] = (float) \App\Models\InvoiceSettings::get('tax_rate', 19);
                                }
                                return $data;
                            })
                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                                $data['currency'] = 'COP';
                                // Asegurar que tax_percentage sea un valor entero (19 no 0.19)
                                if (isset($data['tax_percentage'])) {
                                    $taxRate = (float) $data['tax_percentage'];
                                    if ($taxRate < 1) {
                                        // Si es decimal (0.19), convertir a porcentaje (19)
                                        $data['tax_percentage'] = $taxRate * 100;
                                    }
                                } else {
                                    $data['tax_percentage'] = (float) \App\Models\InvoiceSettings::get('tax_rate', 19);
                                }
                                return $data;
                            }),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Número de factura con icono y formato destacado
                Tables\Columns\TextColumn::make('serial_number')
                    ->label('N° Factura')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->icon('heroicon-m-document-text')
                    ->copyable()
                    ->copyMessage('Número copiado')
                    ->copyMessageDuration(1500),

                // Tipo de factura con traducción al español
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn($state): string => match ($state->value) {
                        'invoice' => 'Factura',
                        'estimate' => 'Cotización',
                        'credit_note' => 'Nota Crédito',
                        'debit_note' => 'Nota Débito',
                        default => $state->value,
                    })
                    ->color(fn(InvoiceType $state): string => $state->getColor())
                    ->icon(fn($state): string => match ($state->value) {
                        'invoice' => 'heroicon-m-document-check',
                        'estimate' => 'heroicon-m-document-magnifying-glass',
                        'credit_note' => 'heroicon-m-document-minus',
                        'debit_note' => 'heroicon-m-document-plus',
                        default => 'heroicon-m-document',
                    }),

                // Información del paciente mejorada con avatar
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Paciente')
                    ->formatStateUsing(
                        fn($record) =>
                        $record->client ?
                        $record->client->name . ' ' . ($record->client->apellido ?? '') :
                        'Sin paciente asignado'
                    )
                    ->description(
                        fn($record): ?string =>
                        $record->client ?
                        ($record->client->numero_documento ? 'Doc: ' . $record->client->numero_documento : 'Sin documento')
                        : null
                    )
                    ->searchable(['client.name', 'client.apellido', 'client.numero_documento'])
                    ->sortable()
                    ->weight(FontWeight::Medium)
                    ->wrap(),

                // Contador de servicios con mejor presentación
                Tables\Columns\TextColumn::make('items_count')
                    ->label('Servicios')
                    ->counts('items')
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-m-list-bullet')
                    ->sortable(),

                // Estado con traducción al español y mejor presentación
                Tables\Columns\TextColumn::make('state')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn($state): string => match ($state->value) {
                        'draft' => 'Borrador',
                        'sent' => 'Enviada',
                        'paid' => 'Pagada',
                        'overdue' => 'Vencida',
                        'cancelled' => 'Cancelada',
                        'partial' => 'Pago Parcial',
                        default => $state->value,
                    })
                    ->color(fn(InvoiceState $state): string => $state->getColor())
                    ->icon(fn($state): string => match ($state->value) {
                        'draft' => 'heroicon-m-pencil-square',
                        'sent' => 'heroicon-m-paper-airplane',
                        'paid' => 'heroicon-m-check-circle',
                        'overdue' => 'heroicon-m-exclamation-triangle',
                        'cancelled' => 'heroicon-m-x-circle',
                        'partial' => 'heroicon-m-clock',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->sortable(),

                // Total con formato mejorado similar a las citas
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->formatStateUsing(function ($state): string {
                        if (!$state)
                            return 'Sin monto';

                        // Si es un objeto Money, obtener el valor
                        if ($state instanceof \Brick\Money\Money) {
                            $amount = $state->getAmount()->toFloat();
                        } else {
                            $amount = (float) $state;
                        }

                        return '$' . number_format($amount, 0, ',', '.');
                    })
                    ->weight(FontWeight::Bold)
                    ->color('success')
                    ->icon('heroicon-m-banknotes')
                    ->sortable(),

                // Fecha de vencimiento con indicador visual
                Tables\Columns\TextColumn::make('due_at')
                    ->label('Vencimiento')
                    ->date('d/m/Y')
                    ->sortable()
                    ->icon('heroicon-m-calendar-days')
                    ->color(function ($record): string {
                        if (!$record->due_at)
                            return 'gray';
                        $dueDate = \Carbon\Carbon::parse($record->due_at);
                        $now = now();

                        if ($dueDate->isPast() && $record->state->value !== 'paid') {
                            return 'danger'; // Vencida y no pagada
                        } elseif ($dueDate->diffInDays($now) <= 7 && $record->state->value !== 'paid') {
                            return 'warning'; // Próxima a vencer
                        }
                        return 'gray';
                    })
                    ->description(function ($record): ?string {
                        if (!$record->due_at)
                            return null;
                        $dueDate = \Carbon\Carbon::parse($record->due_at);
                        $now = now();

                        if ($dueDate->isPast() && $record->state->value !== 'paid') {
                            return 'Vencida hace ' . $dueDate->diffForHumans($now, true);
                        } elseif ($dueDate->diffInDays($now) <= 7 && $record->state->value !== 'paid') {
                            return 'Vence en ' . $dueDate->diffForHumans($now, true);
                        }
                        return null;
                    }),

                // Fecha de creación mejorada
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->icon('heroicon-m-clock')
                    ->color('gray')
                    ->description(
                        fn($record): string =>
                        'Hace ' . $record->created_at->diffForHumans()
                    )
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo de Factura')
                    ->options([
                        'invoice' => 'Factura',
                        'estimate' => 'Cotización',
                        'credit_note' => 'Nota Crédito',
                        'debit_note' => 'Nota Débito',
                    ])
                    ->native(false)
                    ->indicator('Tipo'),

                Tables\Filters\SelectFilter::make('state')
                    ->label('Estado')
                    ->options([
                        'draft' => 'Borrador',
                        'sent' => 'Enviada',
                        'paid' => 'Pagada',
                        'overdue' => 'Vencida',
                        'cancelled' => 'Cancelada',
                        'partial' => 'Pago Parcial',
                    ])
                    ->native(false)
                    ->indicator('Estado'),

                Tables\Filters\Filter::make('overdue')
                    ->label('Facturas Vencidas')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->where('due_at', '<', now())->where('state', '!=', 'paid')
                    )
                    ->indicator('Vencidas'),

                Tables\Filters\Filter::make('amount_range')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('amount_from')
                                    ->label('Monto desde')
                                    ->numeric()
                                    ->prefix('$'),
                                Forms\Components\TextInput::make('amount_until')
                                    ->label('Monto hasta')
                                    ->numeric()
                                    ->prefix('$'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['amount_from'],
                                fn(Builder $query, $amount): Builder => $query->where('total_amount', '>=', $amount),
                            )
                            ->when(
                                $data['amount_until'],
                                fn(Builder $query, $amount): Builder => $query->where('total_amount', '<=', $amount),
                            );
                    })
                    ->indicator('Rango de Monto'),

                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('created_from')
                                    ->label('Creada desde')
                                    ->native(false),
                                Forms\Components\DatePicker::make('created_until')
                                    ->label('Creada hasta')
                                    ->native(false),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicator('Rango de Fechas'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('info'),

                    Tables\Actions\EditAction::make()
                        ->color('warning'),

                    Tables\Actions\Action::make('view_pdf')
                        ->label('Ver PDF')
                        ->icon('heroicon-o-eye')
                        ->color('gray')
                        ->url(fn(Invoice $record): string => route('invoices.pdf', $record))
                        ->openUrlInNewTab(),

                    Tables\Actions\Action::make('download_pdf')
                        ->label('Descargar PDF')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->url(fn(Invoice $record): string => route('invoices.download', $record))
                        ->openUrlInNewTab(),

                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation(),
                ])
                    ->tooltip('Acciones')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('update_state')
                        ->label('Cambiar Estado')
                        ->icon('heroicon-o-pencil-square')
                        ->form([
                            Forms\Components\Select::make('state')
                                ->label('Nuevo Estado')
                                ->options([
                                    'draft' => 'Borrador',
                                    'sent' => 'Enviada',
                                    'paid' => 'Pagada',
                                    'overdue' => 'Vencida',
                                    'cancelled' => 'Cancelada',
                                    'partial' => 'Pago Parcial',
                                ])
                                ->required()
                                ->native(false),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each(function (Invoice $record) use ($data) {
                                $record->update(['state' => $data['state']]);
                            });

                            Notification::make()
                                ->title('Estados actualizados exitosamente')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('export_pdf')
                        ->label('Exportar PDFs')
                        ->icon('heroicon-o-document-arrow-down')
                        ->action(function (Collection $records) {
                            // Aquí implementarías la lógica para exportar múltiples PDFs
                            Notification::make()
                                ->title('Exportación iniciada')
                                ->body('Se están generando los PDFs de las facturas seleccionadas')
                                ->info()
                                ->send();
                        }),

                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Crear Primera Factura')
                    ->icon('heroicon-o-plus'),
            ])
            ->emptyStateDescription('No hay facturas registradas. Comienza creando tu primera factura.')
            ->emptyStateIcon('heroicon-o-document-text')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->defaultSort('created_at', 'desc')
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession();
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('state', InvoiceState::Draft)->count();
    }

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
        return Auth::check() && Gate::allows('view_any_invoice');
    }

    public static function canView(Model $record): bool
    {
        return Auth::check() && Gate::allows('view_invoice', $record);
    }

    public static function canCreate(): bool
    {
        return Auth::check() && Gate::allows('create_invoice');
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::check() && Gate::allows('update_invoice', $record);
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::check() && Gate::allows('delete_invoice', $record);
    }

    public static function canDeleteAny(): bool
    {
        return Auth::check() && Gate::allows('delete_any_invoice');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Gate::allows('view_any_invoice');
    }

    /**
     * Mutate form data before creating the record
     */
    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['currency'] = 'COP';

        // Solo limpiar campos problemáticos específicos, mantener la información válida del cliente
        if (isset($data['buyer_information']['fields']) && is_array($data['buyer_information']['fields'])) {
            $fields = $data['buyer_information']['fields'];

            // Eliminar solo campos problemáticos específicos
            if (isset($fields['Documento']) && ($fields['Documento'] === null || $fields['Documento'] === '')) {
                unset($data['buyer_information']['fields']['Documento']);
            }

            if (isset($fields['Tipo de Cliente']) && $fields['Tipo de Cliente'] === 'Natural') {
                unset($data['buyer_information']['fields']['Tipo de Cliente']);
            }
        }

        return $data;
    }

    /**
     * Mutate form data before saving the record
     */
    public static function mutateFormDataBeforeSave(array $data): array
    {
        $data['currency'] = 'COP';

        // Solo limpiar campos problemáticos específicos, mantener la información válida del cliente
        if (isset($data['buyer_information']['fields']) && is_array($data['buyer_information']['fields'])) {
            $fields = $data['buyer_information']['fields'];

            // Eliminar solo campos problemáticos específicos
            if (isset($fields['Documento']) && ($fields['Documento'] === null || $fields['Documento'] === '')) {
                unset($data['buyer_information']['fields']['Documento']);
            }

            if (isset($fields['Tipo de Cliente']) && $fields['Tipo de Cliente'] === 'Natural') {
                unset($data['buyer_information']['fields']['Tipo de Cliente']);
            }
        }

        // Asegurarse de que los items tengan la información correcta
        if (isset($data['items'])) {
            $defaultTaxRate = (float) \App\Models\InvoiceSettings::get('tax_rate', 19);
            foreach ($data['items'] as $index => &$item) {
                $item['currency'] = 'COP';
                $item['order'] = $index + 1;

                // Asegurar que tax_percentage tenga un valor válido
                if (!isset($item['tax_percentage']) || $item['tax_percentage'] === null) {
                    $item['tax_percentage'] = $defaultTaxRate;
                }
            }
        }

        return $data;
    }
}
