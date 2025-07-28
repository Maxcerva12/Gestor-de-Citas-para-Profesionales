<?php

namespace App\Filament\Resources\InvoiceSettingsResource\Pages;

use App\Filament\Resources\InvoiceSettingsResource;
use App\Models\InvoiceSettings;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;

class ManageInvoiceSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = InvoiceSettingsResource::class;

    protected static string $view = 'filament.pages.manage-invoice-settings';

    protected static ?string $title = 'Configuración de Facturas';

    public ?array $data = [];

    public function mount(): void
    {
        $this->data = [
            'company_name' => InvoiceSettings::get('company_name', ''),
            'company_email' => InvoiceSettings::get('company_email', ''),
            'company_phone' => InvoiceSettings::get('company_phone', ''),
            'company_tax_number' => InvoiceSettings::get('company_tax_number', ''),
            'company_address_street' => InvoiceSettings::get('company_address_street', ''),
            'company_address_city' => InvoiceSettings::get('company_address_city', ''),
            'company_address_state' => InvoiceSettings::get('company_address_state', ''),
            'company_address_postal_code' => InvoiceSettings::get('company_address_postal_code', ''),
            'company_address_country' => InvoiceSettings::get('company_address_country', 'Colombia'),
            'company_logo' => InvoiceSettings::get('company_logo', ''),
            'pdf_font' => InvoiceSettings::get('pdf_font', 'Helvetica'),
            'pdf_template_color' => InvoiceSettings::get('pdf_template_color', '#1e40af'),
            'tax_rate' => InvoiceSettings::get('tax_rate', 19),
            'currency' => InvoiceSettings::get('currency', 'COP'),
        ];

        $this->settingsForm->fill($this->data);
    }

    protected function getForms(): array
    {
        return [
            'settingsForm' => $this->form(
                $this->makeForm()
                    ->schema($this->getFormSchema())
                    ->statePath('data')
            ),
        ];
    }

    public function form(Form $form): Form
    {
        return $form;
    }

    protected function getFormSchema(): array
    {
        return [
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
                                        ->required()
                                        ->maxLength(255),

                                    TextInput::make('company_email')
                                        ->label('Email de la Empresa')
                                        ->email()
                                        ->required(),

                                    TextInput::make('company_phone')
                                        ->label('Teléfono de la Empresa')
                                        ->tel(),

                                    TextInput::make('company_tax_number')
                                        ->label('NIT/RUT')
                                        ->required(),
                                ])->columns(2),

                            Section::make('Dirección de la Empresa')
                                ->description('Configure la dirección que aparecerá en las facturas')
                                ->schema([
                                    TextInput::make('company_address_street')
                                        ->label('Dirección')
                                        ->required()
                                        ->columnSpanFull(),

                                    TextInput::make('company_address_city')
                                        ->label('Ciudad')
                                        ->required(),

                                    TextInput::make('company_address_state')
                                        ->label('Departamento/Estado'),

                                    TextInput::make('company_address_postal_code')
                                        ->label('Código Postal'),

                                    TextInput::make('company_address_country')
                                        ->label('País')
                                        ->default('Colombia')
                                        ->required(),
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
                                        ->imageEditor(),
                                ]),

                            Section::make('Tipografía y Colores')
                                ->description('Personalice la apariencia de las facturas')
                                ->schema([
                                    Select::make('pdf_font')
                                        ->label('Tipografía')
                                        ->options([
                                            'Helvetica' => 'Helvetica',
                                            'Arial' => 'Arial',
                                            'Times-Roman' => 'Times New Roman',
                                            'Courier' => 'Courier',
                                        ])
                                        ->default('Helvetica')
                                        ->required(),

                                    ColorPicker::make('pdf_template_color')
                                        ->label('Color Principal')
                                        ->default('#1e40af')
                                        ->required(),
                                ])->columns(2),
                        ]),

                    Tabs\Tab::make('Configuración Fiscal')
                        ->icon('heroicon-o-calculator')
                        ->schema([
                            Section::make('Impuestos y Tasas')
                                ->description('Configure los impuestos aplicables en Colombia')
                                ->schema([
                                    TextInput::make('tax_rate')
                                        ->label('Tasa de IVA (%)')
                                        ->numeric()
                                        ->default(19)
                                        ->required()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->step(0.01)
                                        ->suffix('%'),

                                    Select::make('currency')
                                        ->label('Moneda')
                                        ->options([
                                            'COP' => 'Peso Colombiano (COP)',
                                            'USD' => 'Dólar Americano (USD)',
                                            'EUR' => 'Euro (EUR)',
                                        ])
                                        ->default('COP')
                                        ->required(),
                                ])->columns(2),
                        ]),

                    Tabs\Tab::make('Vista Previa')
                        ->icon('heroicon-o-eye')
                        ->schema([
                            Section::make('Vista Previa de la Factura')
                                ->description('Vea cómo se verá su factura con la configuración actual')
                                ->schema([
                                    \Filament\Forms\Components\Placeholder::make('preview')
                                        ->label('')
                                        ->content(function () {
                                            return new \Illuminate\Support\HtmlString(
                                                view('filament.components.invoice-preview', [
                                                    'companyName' => InvoiceSettings::get('company_name', 'Mi Empresa'),
                                                    'companyEmail' => InvoiceSettings::get('company_email', 'email@empresa.com'),
                                                    'companyPhone' => InvoiceSettings::get('company_phone', '+57 123 456 7890'),
                                                    'color' => InvoiceSettings::get('pdf_template_color', '#1e40af'),
                                                    'font' => InvoiceSettings::get('pdf_font', 'Helvetica'),
                                                    'logo' => InvoiceSettings::getCompanyLogo(),
                                                ])->render()
                                            );
                                        }),

                                    \Filament\Forms\Components\Actions::make([
                                        \Filament\Forms\Components\Actions\Action::make('downloadPreview')
                                            ->label('Descargar PDF de Vista Previa')
                                            ->icon('heroicon-o-document-arrow-down')
                                            ->color('primary')
                                            ->url(fn() => route('invoice.preview'))
                                            ->openUrlInNewTab(),
                                    ])->fullWidth(),
                                ]),
                        ]),
                ])
                ->columnSpanFull()
                ->persistTabInQueryString(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('save')
                ->label('Guardar Configuración')
                ->icon('heroicon-o-check')
                ->color('success')
                ->action('save'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('reset')
                ->label('Restaurar Valores por Defecto')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->requiresConfirmation()
                ->action('resetToDefaults'),
        ];
    }

    public function save(): void
    {
        $data = $this->settingsForm->getState();

        foreach ($data as $key => $value) {
            if ($value !== null && $value !== '') {
                InvoiceSettings::set($key, $value);
            }
        }

        Notification::make()
            ->title('Configuración guardada')
            ->body('La configuración de facturas se ha actualizado correctamente.')
            ->success()
            ->send();
    }

    public function resetToDefaults(): void
    {
        $defaults = [
            'company_name' => '',
            'company_email' => '',
            'company_phone' => '',
            'company_tax_number' => '',
            'company_address_street' => '',
            'company_address_city' => '',
            'company_address_state' => '',
            'company_address_postal_code' => '',
            'company_address_country' => 'Colombia',
            'company_logo' => '',
            'pdf_font' => 'Helvetica',
            'pdf_template_color' => '#1e40af',
            'tax_rate' => 19,
            'currency' => 'COP',
        ];

        foreach ($defaults as $key => $value) {
            InvoiceSettings::set($key, $value);
        }

        $this->data = $defaults;
        $this->settingsForm->fill($defaults);

        Notification::make()
            ->title('Configuración restaurada')
            ->body('Se han restaurado los valores por defecto.')
            ->success()
            ->send();
    }
}
