<?php

namespace App\Filament\Resources;

use App\Enums\InvoiceState;
use App\Enums\InvoiceType;
use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Client;
use App\Models\Invoice;
use Brick\Money\Money;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
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

                        Forms\Components\DateTimePicker::make('paid_at')
                            ->label('Fecha de Pago')
                            ->native(false)
                            ->visible(fn($record) => $record?->state === InvoiceState::Paid),

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
                            ->keyLabel('Campo')
                            ->valueLabel('Valor')
                            ->default([
                                'Tipo de Documento' => 'Cédula de Ciudadanía',
                                'EPS' => '',
                            ]),
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
                Tables\Columns\TextColumn::make('serial_number')
                    ->label('Número')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn(InvoiceType $state): string => $state->getColor()),

                Tables\Columns\TextColumn::make('client.name')
                    ->label('Paciente')
                    ->formatStateUsing(
                        fn($record) =>
                        $record->client ?
                        $record->client->name . ' ' . ($record->client->apellido ?? '') :
                        'Sin paciente'
                    )
                    ->searchable(['client.name', 'client.apellido', 'client.numero_documento']),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('Servicios')
                    ->counts('items')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('state')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(InvoiceState $state): string => $state->getColor()),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('COP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_at')
                    ->label('Vencimiento')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options(InvoiceType::class),

                Tables\Filters\SelectFilter::make('state')
                    ->label('Estado')
                    ->options(InvoiceState::class),

                Tables\Filters\Filter::make('overdue')
                    ->label('Vencidas')
                    ->query(fn(Builder $query): Builder => $query->where('due_at', '<', now())->where('state', '!=', 'paid')),
            ])
            ->actions([
                Tables\Actions\Action::make('view_pdf')
                    ->label('Ver PDF')
                    ->icon('heroicon-o-eye')
                    ->url(fn(Invoice $record): string => route('invoices.pdf', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('download_pdf')
                    ->label('Descargar PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn(Invoice $record): string => route('invoices.download', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('mark_paid')
                    ->label('Marcar como Pagada')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (Invoice $record) {
                        $record->update([
                            'state' => InvoiceState::Paid,
                            'paid_at' => now(),
                        ]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('¿Confirmar pago de la factura?')
                    ->modalDescription('Esta acción marcará la factura como pagada y registrará la fecha actual como fecha de pago.')
                    ->modalSubmitActionLabel('Confirmar Pago')
                    ->visible(fn(Invoice $record): bool => $record->state !== InvoiceState::Paid),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
        return $data;
    }

    /**
     * Mutate form data before saving the record
     */
    public static function mutateFormDataBeforeSave(array $data): array
    {
        $data['currency'] = 'COP';

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
