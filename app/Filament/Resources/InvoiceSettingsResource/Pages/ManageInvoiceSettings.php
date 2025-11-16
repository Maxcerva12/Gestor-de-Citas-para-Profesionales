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
use Illuminate\Support\Facades\Storage;

class ManageInvoiceSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = InvoiceSettingsResource::class;

    protected static string $view = 'filament.pages.manage-invoice-settings';

    protected static ?string $title = 'Configuración de Facturas';

    // Configuración para colapsar el sidebar por defecto
    protected static bool $shouldCollapseSidebar = true;

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
            'invoice_template' => InvoiceSettings::get('invoice_template', 'colombia.layout'),
            'discount_enabled' => InvoiceSettings::get('discount_enabled', 'false') === 'true',
            'discount_percentage' => (float) InvoiceSettings::get('discount_percentage', 0),
        ];

        $this->settingsForm->fill($this->data);

        // Forzar el colapso del sidebar al cargar la página
        $this->dispatch('collapse-sidebar');
    }

    public function getViewData(): array
    {
        return array_merge(parent::getViewData(), [
            'sidebarCollapsed' => true,
        ]);
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
                                        ->default('Cienaga')
                                        ->required(),

                                    TextInput::make('company_address_state')
                                        ->label('Departamento/Estado')
                                        ->default('Magdalena')
                                        ->required(),

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
                                    Select::make('invoice_template')
                                        ->label('Plantilla de Factura')
                                        ->native(false)
                                        ->options([
                                            'default.layout' => 'Plantilla por Defecto',
                                            'colombia.layout' => 'Plantilla Colombia',
                                        ])
                                        ->default('colombia.layout')
                                        ->required()
                                        ->live(),

                                    Select::make('pdf_font')
                                        ->label('Tipografía')
                                        ->native(false)
                                        ->options([
                                            'Helvetica' => 'Helvetica',
                                            'Arial' => 'Arial',
                                            'Times-Roman' => 'Times New Roman',
                                            'Courier' => 'Courier',
                                        ])
                                        ->default('Helvetica')
                                        ->required()
                                        ->live(),

                                    ColorPicker::make('pdf_template_color')
                                        ->label('Color Principal')
                                        ->default('#1e40af')
                                        ->required()
                                        ->live(),
                                ])->columns(2),
                        ]),

                    Tabs\Tab::make('Configuración Fiscal')
                        ->icon('heroicon-o-calculator')
                        ->schema([
                            Section::make('Descuentos')
                                ->description('Configure descuentos globales para las facturas')
                                ->schema([
                                    \Filament\Forms\Components\Toggle::make('discount_enabled')
                                        ->label('Habilitar Descuentos')
                                        ->helperText('Active esta opción para aplicar un descuento global a todas las facturas')
                                        ->default(false)
                                        ->live(),

                                    TextInput::make('discount_percentage')
                                        ->label('Porcentaje de Descuento (%)')
                                        ->numeric()
                                        ->default(0)
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->step(0.01)
                                        ->suffix('%')
                                        ->helperText('Porcentaje de descuento que se aplicará sobre el subtotal (antes de impuestos)')
                                        ->hidden(fn($get) => !$get('discount_enabled'))
                                        ->required(fn($get) => $get('discount_enabled')),
                                ])->columns(2),

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
                                        ->native(false)
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
                                        ->content(function ($get) {
                                            try {
                                                $selectedTemplate = $get('invoice_template') ?: InvoiceSettings::get('invoice_template', 'colombia.layout');
                                                $color = $get('pdf_template_color') ?: InvoiceSettings::get('pdf_template_color', '#1e40af');
                                                $font = $get('pdf_font') ?: InvoiceSettings::get('pdf_font', 'Helvetica');

                                                // Determinar qué template usar basado en la selección
                                                $templateName = str_replace('.layout', '', $selectedTemplate);

                                                return new \Illuminate\Support\HtmlString(
                                                    view("filament.components.invoice-template-preview", [
                                                        'templateName' => $templateName,
                                                        'companyName' => $get('company_name') ?: InvoiceSettings::get('company_name', 'Mi Empresa'),
                                                        'companyEmail' => $get('company_email') ?: InvoiceSettings::get('company_email', 'email@empresa.com'),
                                                        'companyPhone' => $get('company_phone') ?: InvoiceSettings::get('company_phone', '+57 123 456 7890'),
                                                        'color' => $color,
                                                        'font' => $font,
                                                        'logo' => $this->getLogoForPreview($get),
                                                    ])->render()
                                                );
                                            } catch (\Exception $e) {
                                                return new \Illuminate\Support\HtmlString(
                                                    '<div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                                                        <p class="text-red-800 font-semibold">Error al cargar la vista previa:</p>
                                                        <p class="text-red-600 text-sm mt-2">' . $e->getMessage() . '</p>
                                                    </div>'
                                                );
                                            }
                                        })
                                        ->live(),

                                    \Filament\Forms\Components\Actions::make([
                                        \Filament\Forms\Components\Actions\Action::make('downloadPreview')
                                            ->label('Descargar PDF de Vista Previa')
                                            ->icon('heroicon-o-document-arrow-down')
                                            ->color('primary')
                                            ->url(function ($get) {
                                                $template = $get('invoice_template') ?: InvoiceSettings::get('invoice_template', 'colombia.layout');
                                                $color = $get('pdf_template_color') ?: InvoiceSettings::get('pdf_template_color', '#1e40af');
                                                $font = $get('pdf_font') ?: InvoiceSettings::get('pdf_font', 'Helvetica');

                                                return route('invoice.preview', [
                                                    'template' => $template,
                                                    'color' => $color,
                                                    'font' => $font,
                                                ]);
                                            })
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
                ->color('primary')
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
            // Para discount_enabled, convertir booleano a string
            if ($key === 'discount_enabled') {
                InvoiceSettings::set($key, $value ? 'true' : 'false');
            }
            // Para discount_percentage, permitir 0
            elseif ($key === 'discount_percentage') {
                InvoiceSettings::set($key, $value ?? 0);
            }
            // Para otros valores, mantener la lógica existente
            elseif ($value !== null && $value !== '') {
                InvoiceSettings::set($key, $value);
            }
        }

        // Limpiar toda la caché de configuraciones
        InvoiceSettings::clearCache();

        // También limpiar la caché general de Laravel
        \Cache::flush();

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
            'invoice_template' => 'colombia.layout',
            'discount_enabled' => false,
            'discount_percentage' => 0,
        ];

        foreach ($defaults as $key => $value) {
            if ($key === 'discount_enabled') {
                InvoiceSettings::set($key, 'false');
            } else {
                InvoiceSettings::set($key, $value);
            }
        }

        $this->data = $defaults;
        $this->settingsForm->fill($defaults);

        Notification::make()
            ->title('Configuración restaurada')
            ->body('Se han restaurado los valores por defecto.')
            ->success()
            ->send();
    }

    /**
     * Obtener el logo para la vista previa
     */
    protected function getLogoForPreview($get): ?string
    {
        $logo = $get('company_logo');

        if (!$logo) {
            return InvoiceSettings::getCompanyLogo();
        }

        // Si es un array (archivo recién subido)
        if (is_array($logo)) {
            $logoPath = $logo[0] ?? null;
            if ($logoPath) {
                return \Storage::url($logoPath);
            }
        }

        // Si es una string (ruta existente)
        if (is_string($logo)) {
            // Si ya es una URL data:, devolverla tal como está
            if (str_starts_with($logo, 'data:')) {
                return $logo;
            }

            // Si es una ruta de archivo, convertir a URL
            try {
                return \Storage::url($logo);
            } catch (\Exception $e) {
                return InvoiceSettings::getCompanyLogo();
            }
        }

        return InvoiceSettings::getCompanyLogo();
    }

    protected function getLayoutData(): array
    {
        return array_merge(parent::getLayoutData(), [
            'isSidebarCollapsed' => true,
        ]);
    }

    public function getExtraHeadContent(): ?string
    {
        return '
        <style>
            .fi-invoice-settings .fi-sidebar {
                transform: translateX(-100%) !important;
            }
            .fi-invoice-settings .fi-main {
                margin-left: 0 !important;
                padding-left: 0 !important;
            }
            .fi-invoice-settings .fi-sidebar-collapsed .fi-sidebar {
                width: 0 !important;
                min-width: 0 !important;
            }
        </style>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                setTimeout(function() {
                    const sidebar = document.querySelector(".fi-sidebar");
                    const main = document.querySelector(".fi-main");
                    const toggleButton = document.querySelector("[data-sidebar-toggle]") || 
                                       document.querySelector(".fi-sidebar-toggle") ||
                                       document.querySelector("button[aria-label*=\"sidebar\"]");
                    
                    if (sidebar && sidebar.classList.contains("fi-sidebar-open")) {
                        if (toggleButton) {
                            toggleButton.click();
                        }
                    }
                }, 50);
            });
        </script>';
    }

    public function getExtraBodyAttributes(): array
    {
        return [
            'class' => 'fi-invoice-settings-page'
        ];
    }
}
