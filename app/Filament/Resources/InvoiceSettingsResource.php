<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceSettingsResource\Pages;
use App\Models\InvoiceSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;

class InvoiceSettingsResource extends Resource
{
    protected static ?string $model = InvoiceSettings::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Configuración de Facturas';

    protected static ?string $modelLabel = 'Configuración de Factura';

    protected static ?string $pluralModelLabel = 'Configuraciones de Facturas';

    protected static ?string $navigationGroup = 'Configuración';

    protected static ?int $navigationSort = 10;

    protected static ?string $slug = 'invoice-settings';



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

    // Comprobaciones de permisos personalizadas para InvoiceSettings
    public static function canViewAny(): bool
    {
        return \Auth::check() && \Gate::allows('view_any_invoice::settings');
    }

    public static function canView($record): bool
    {
        return \Auth::check() && \Gate::allows('view_invoice::settings', $record);
    }

    public static function canCreate(): bool
    {
        return \Auth::check() && \Gate::allows('create_invoice::settings');
    }

    public static function canEdit($record): bool
    {
        return \Auth::check() && \Gate::allows('update_invoice::settings', $record);
    }

    public static function canDelete($record): bool
    {
        return \Auth::check() && \Gate::allows('delete_invoice::settings', $record);
    }

    public static function canDeleteAny(): bool
    {
        return \Auth::check() && \Gate::allows('delete_any_invoice::settings');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return \Auth::check() && \Gate::allows('view_any_invoice::settings');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Configuración')
                    ->tabs([
                        Tabs\Tab::make('Información de la Empresa')
                            ->icon('heroicon-o-building-office')
                            ->schema([
                                Section::make('Datos Básicos de la Empresa')
                                    ->description('Configure la información básica que aparecerá en las facturas')
                                    ->schema([
                                        TextInput::make('company_name')
                                            ->label('Nombre de la Empresa')
                                            ->default(fn() => InvoiceSettings::get('company_name', ''))
                                            ->required()
                                            ->maxLength(255)
                                            ->live()
                                            ->afterStateUpdated(fn($state) => InvoiceSettings::set('company_name', $state)),

                                        TextInput::make('company_email')
                                            ->label('Email de la Empresa')
                                            ->email()
                                            ->default(fn() => InvoiceSettings::get('company_email', ''))
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(fn($state) => InvoiceSettings::set('company_email', $state)),

                                        TextInput::make('company_phone')
                                            ->label('Teléfono de la Empresa')
                                            ->tel()
                                            ->default(fn() => InvoiceSettings::get('company_phone', ''))
                                            ->live()
                                            ->afterStateUpdated(fn($state) => InvoiceSettings::set('company_phone', $state)),

                                        TextInput::make('company_tax_number')
                                            ->label('NIT/RUT')
                                            ->default(fn() => InvoiceSettings::get('company_tax_number', ''))
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(fn($state) => InvoiceSettings::set('company_tax_number', $state)),
                                    ])->columns(2),

                                Section::make('Dirección de la Empresa')
                                    ->description('Configure la dirección que aparecerá en las facturas')
                                    ->schema([
                                        TextInput::make('company_address_street')
                                            ->label('Dirección')
                                            ->default(fn() => InvoiceSettings::get('company_address_street', ''))
                                            ->required()
                                            ->columnSpanFull()
                                            ->live()
                                            ->afterStateUpdated(fn($state) => InvoiceSettings::set('company_address_street', $state)),

                                        TextInput::make('company_address_city')
                                            ->label('Ciudad')
                                            ->default(fn() => InvoiceSettings::get('company_address_city', ''))
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(fn($state) => InvoiceSettings::set('company_address_city', $state)),

                                        TextInput::make('company_address_state')
                                            ->label('Departamento/Estado')
                                            ->default(fn() => InvoiceSettings::get('company_address_state', ''))
                                            ->live()
                                            ->afterStateUpdated(fn($state) => InvoiceSettings::set('company_address_state', $state)),

                                        TextInput::make('company_address_postal_code')
                                            ->label('Código Postal')
                                            ->default(fn() => InvoiceSettings::get('company_address_postal_code', ''))
                                            ->live()
                                            ->afterStateUpdated(fn($state) => InvoiceSettings::set('company_address_postal_code', $state)),

                                        TextInput::make('company_address_country')
                                            ->label('País')
                                            ->default(fn() => InvoiceSettings::get('company_address_country', 'Colombia'))
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(fn($state) => InvoiceSettings::set('company_address_country', $state)),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('Diseño y Apariencia')
                            ->icon('heroicon-o-paint-brush')
                            ->schema([
                                Section::make('Logo de la Empresa')
                                    ->description('Suba el logo que aparecerá en las facturas')
                                    ->schema([
                                        FileUpload::make('company_logo')
                                            ->label('Logo de la Empresa')
                                            ->image()
                                            ->directory('company')
                                            ->visibility('public')
                                            ->maxSize(2048)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/svg+xml'])
                                            ->imageEditor()
                                            ->live()
                                            ->afterStateUpdated(function ($state) {
                                                if ($state) {
                                                    InvoiceSettings::set('company_logo', $state);
                                                }
                                            }),
                                    ]),

                                Section::make('Tipografía y Colores')
                                    ->description('Personalice la apariencia de las facturas')
                                    ->native(false)
                                    ->schema([
                                        Select::make('invoice_template')
                                            ->label('Plantilla de Factura')
                                            ->native(false)
                                            ->options([
                                                'default.layout' => 'Plantilla por Defecto',
                                                'colombia.layout' => 'Plantilla Colombia',
                                            ])
                                            ->default(fn() => InvoiceSettings::get('invoice_template', 'colombia.layout'))
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(fn($state) => InvoiceSettings::set('invoice_template', $state)),

                                        Select::make('pdf_font')
                                            ->label('Tipografía')
                                            ->options([
                                                'Helvetica' => 'Helvetica',
                                                'Arial' => 'Arial',
                                                'Times-Roman' => 'Times New Roman',
                                                'Courier' => 'Courier',
                                            ])
                                            ->native(false)
                                            ->default(fn() => InvoiceSettings::get('pdf_font', 'Helvetica'))
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(fn($state) => InvoiceSettings::set('pdf_font', $state)),

                                        ColorPicker::make('pdf_template_color')
                                            ->label('Color Principal')
                                            ->default(fn() => InvoiceSettings::get('pdf_template_color', '#1e40af'))
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(fn($state) => InvoiceSettings::set('pdf_template_color', $state)),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('Configuración Fiscal')
                            ->icon('heroicon-o-calculator')
                            ->schema([
                                Section::make('Descuentos')
                                    ->description('Configure descuentos globales para las facturas')
                                    ->schema([
                                        Forms\Components\Toggle::make('discount_enabled')
                                            ->label('Habilitar Descuentos')
                                            ->helperText('Active esta opción para aplicar un descuento global a todas las facturas')
                                            ->default(function () {
                                                $value = InvoiceSettings::get('discount_enabled', 'false');
                                                return $value === 'true' || $value === true || $value === 1 || $value === '1';
                                            })
                                            ->live()
                                            ->afterStateUpdated(function ($state) {
                                                InvoiceSettings::set('discount_enabled', $state ? 'true' : 'false');
                                            }),

                                        TextInput::make('discount_percentage')
                                            ->label('Porcentaje de Descuento (%)')
                                            ->numeric()
                                            ->default(fn() => InvoiceSettings::get('discount_percentage', 0))
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->step(0.01)
                                            ->suffix('%')
                                            ->helperText('Porcentaje de descuento que se aplicará sobre el subtotal (antes de impuestos)')
                                            ->hidden(function ($get) {
                                                $enabled = $get('discount_enabled');
                                                return !$enabled;
                                            })
                                            ->required(function ($get) {
                                                return $get('discount_enabled') == true;
                                            })
                                            ->live()
                                            ->afterStateUpdated(fn($state) => InvoiceSettings::set('discount_percentage', $state ?? 0)),
                                    ])->columns(2),

                                Section::make('Impuestos y Tasas')
                                    ->native(false)
                                    ->description('Configure los impuestos aplicables en Colombia')
                                    ->schema([
                                        TextInput::make('tax_rate')
                                            ->label('Tasa de IVA (%)')
                                            ->numeric()
                                            ->default(fn() => InvoiceSettings::get('tax_rate', 19))
                                            ->required()
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->step(0.01)
                                            ->suffix('%')
                                            ->live()
                                            ->afterStateUpdated(fn($state) => InvoiceSettings::set('tax_rate', $state)),

                                        Select::make('currency')
                                            ->native(false)
                                            ->label('Moneda')
                                            ->options([
                                                'COP' => 'Peso Colombiano (COP)',
                                                'USD' => 'Dólar Americano (USD)',
                                                'EUR' => 'Euro (EUR)',
                                            ])
                                            ->native(false)
                                            ->default(fn() => InvoiceSettings::get('currency', 'COP'))
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(fn($state) => InvoiceSettings::set('currency', $state)),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('Vista Previa')
                            ->icon('heroicon-o-eye')
                            ->schema([
                                Section::make('Vista Previa de la Factura')
                                    ->description('Vea cómo se verá su factura con la configuración actual')
                                    ->schema([
                                        Forms\Components\Placeholder::make('preview')
                                            ->label('')
                                            ->content(function () {
                                                $selectedTemplate = InvoiceSettings::get('invoice_template', 'colombia.layout');
                                                $color = InvoiceSettings::get('pdf_template_color', '#1e40af');
                                                $font = InvoiceSettings::get('pdf_font', 'Helvetica');

                                                // Determinar qué template usar basado en la selección
                                                $templateName = str_replace('.layout', '', $selectedTemplate);

                                                return view("filament.components.invoice-template-preview", [
                                                    'templateName' => $templateName,
                                                    'companyName' => InvoiceSettings::get('company_name', 'Mi Empresa'),
                                                    'companyEmail' => InvoiceSettings::get('company_email', 'email@empresa.com'),
                                                    'companyPhone' => InvoiceSettings::get('company_phone', '+57 123 456 7890'),
                                                    'color' => $color,
                                                    'font' => $font,
                                                    'logo' => InvoiceSettings::getCompanyLogo(),
                                                ]);
                                            }),

                                        Forms\Components\Actions::make([
                                            Action::make('generatePreview')
                                                ->label('Generar PDF de Prueba')
                                                ->icon('heroicon-o-document-arrow-down')
                                                ->color('primary')
                                                ->action(function () {
                                                    // Aquí implementaremos la generación del PDF de prueba
                                                    return redirect()->route('invoice.preview');
                                                }),
                                        ])->fullWidth(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('Configuración')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('value')
                    ->label('Valor')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'string' => 'gray',
                        'integer' => 'info',
                        'boolean' => 'success',
                        'json' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(30)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última Actualización')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'string' => 'Texto',
                        'integer' => 'Número',
                        'boolean' => 'Booleano',
                        'json' => 'JSON',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('key')
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageInvoiceSettings::route('/'),
        ];
    }

}
