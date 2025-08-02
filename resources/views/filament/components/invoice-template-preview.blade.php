@php
    // Definir $logo si no está definido
    $logo = $logo ?? null;
    // Función para obtener la URL del logo de manera segura
    $getLogoUrl = function() use ($logo) {
        if (!$logo) {
            return null;
        }
        
        // Si ya es una URL data: (base64), usarla directamente
        if (is_string($logo) && str_starts_with($logo, 'data:')) {
            return $logo;
        }
        
        // Si ya es una URL completa (http/https), usarla directamente
        if (is_string($logo) && (str_starts_with($logo, 'http://') || str_starts_with($logo, 'https://'))) {
            return $logo;
        }
        
        // Si es un array (nuevo archivo subido), tomar el primer elemento
        if (is_array($logo)) {
            $logoPath = $logo[0] ?? null;
        } else {
            $logoPath = $logo;
        }
        
        // Si tenemos una ruta, convertirla a URL de storage
        if ($logoPath && is_string($logoPath)) {
            try {
                // Si la ruta no comienza con /, agregarla
                if (!str_starts_with($logoPath, '/')) {
                    return \Storage::url($logoPath);
                }
                return $logoPath;
            } catch (\Exception $e) {
                return null;
            }
        }
        
        return null;
    };
    
    $logoUrl = $getLogoUrl();
    
    // Si aún no tenemos logo, intentar obtenerlo directamente de InvoiceSettings
    if (!$logoUrl) {
        $logoUrl = \App\Models\InvoiceSettings::getCompanyLogo();
        if (!$logoUrl) {
            $savedLogo = \App\Models\InvoiceSettings::get('company_logo');
            if ($savedLogo) {
                $logoUrl = \Storage::url($savedLogo);
            }
        }
    }
    
    // Obtener valores de configuración desde la base de datos
    $color = \App\Models\InvoiceSettings::get('pdf_template_color') ?: '#1e40af';
    $font = \App\Models\InvoiceSettings::get('pdf_font') ?: 'Helvetica';
    $companyName = \App\Models\InvoiceSettings::get('company_name');
    $companyEmail = \App\Models\InvoiceSettings::get('company_email');
    $companyPhone = \App\Models\InvoiceSettings::get('company_phone');
    
    // Si no tenemos logo de los parámetros, obtenerlo de la configuración
    if (!$logoUrl) {
        $savedLogo = \App\Models\InvoiceSettings::get('company_logo');
        if ($savedLogo) {
            $logoUrl = \Storage::url($savedLogo);
        }
    }
    
    // Crear un mock invoice object para la vista previa
    $mockInvoice = new class {
        public $serial_number = 'AC250001';
        public $templateData;
        public $logo;
        public $created_at;
        public $due_at;
        public $paid_at = null;
        public $fields;
        public $seller;
        public $buyer;
        public $items;
        public $description = 'Servicios de consultoría y desarrollo web para el mes de julio 2025';
        public $paymentInstructions;
        public $tax_label = 'IVA';
        public $discounts;
        
        public function __construct() {
            global $color, $font, $logoUrl, $companyName, $companyEmail, $companyPhone;
            
            // Asegurar que templateData sea compatible con data_get()
            $this->templateData = collect([
                'color' => $color,
                'font' => $font,
            ]);
            
            // FORZAR el logo - siempre asignar un logo para preview
            $this->logo = $logoUrl ?: 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="150" height="120" viewBox="0 0 150 120"><rect width="150" height="120" fill="#cccccc"/><text x="75" y="65" text-anchor="middle" fill="#666666" font-family="Arial" font-size="14">LOGO</text></svg>');
            
            $this->created_at = now();
            $this->due_at = now()->addDays(30);
            $this->fields = [
                'Régimen Fiscal' => 'Común',
                'Medio de Pago' => 'Contado',
            ];
            
            $this->seller = (object) [
                'name' => $companyName ?: 'Fundación Odontológica Zoila Padilla',
                'company' => $companyName ?: 'Fundación Odontológica Zoila Padilla',
                'email' => $companyEmail ?: 'OdontologicaZoilaPadilla@gmail.com',
                'phone' => $companyPhone ?: '3254789054',
                'tax_number' => '900123456-1',
                'fields' => [
                    'Régimen' => 'Común',
                    'NIT' => '900123456-1'
                ],
                'address' => (object) [
                    'name' => $companyName ?: 'Fundación Odontológica Zoila Padilla',
                    'company' => $companyName ?: 'Fundación Odontológica Zoila Padilla',
                    'street' => 'Cra 32# 22-08',
                    'city' => 'Bogotá',
                    'state' => 'Cundinamarca',
                    'postal_code' => '110111',
                    'country' => 'Colombia',
                    'fields' => [
                        'Régimen' => 'Común',
                        'NIT' => '900123456-1'
                    ],
                ]
            ];
            
            $this->buyer = (object) [
                'name' => 'Nahia Véliz',
                'company' => null,
                'email' => 'raul.olmos@example.com',
                'phone' => '+57 987 654 3210',
                'tax_number' => '12345678',
                'fields' => [
                    'Documento' => '12345678',
                    'Tipo de Cliente' => 'Jurídica',
                    'Régimen Fiscal' => 'Común'
                ],
                'address' => (object) [
                    'name' => 'Nahia Véliz',
                    'company' => null,
                    'street' => 'Avinguda Marcos, 3, 9º C',
                    'city' => 'Segovia del Pozo',
                    'state' => 'Antioquia',
                    'postal_code' => '050001',
                    'country' => 'Colombia',
                    'fields' => [
                        'Documento' => '12345678',
                        'Tipo de Cliente' => 'Jurídica',
                        'Régimen Fiscal' => 'Común'
                    ],
                ],
                'shipping_address' => null,
            ];
            
            // Inicializar discounts como colección vacía
            $this->discounts = collect();
            
            $this->items = collect([
                new class {
                    public $label = 'Servicio Profesional';
                    public $description = 'Servicios de consultoría y desarrollo web para el mes de julio 2025';
                    public $quantity = 1;
                    public $tax_percentage = 19;
                    public $price_per_unit = 100000;
                    public $sub_total_price = 100000;
                    public $unit_price;
                    public $unit_tax = null;
                    
                    public function __construct() {
                        // Crear un mock de unit_price que simule Brick\Money
                        $this->unit_price = new class {
                            private $amount = 100000;
                            
                            public function multipliedBy($qty) {
                                return new class($this->amount * $qty) {
                                    private $amount;
                                    public function __construct($amount) { $this->amount = $amount; }
                                    
                                    public function multipliedBy($factor) {
                                        return new class($this->amount * $factor) {
                                            private $amount;
                                            public function __construct($amount) { $this->amount = $amount; }
                                            
                                            public function dividedBy($divisor, $mode = null) {
                                                return new class($this->amount / $divisor) {
                                                    private $amount;
                                                    public function __construct($amount) { $this->amount = $amount; }
                                                    public function getAmount() { return $this->amount; }
                                                    public function plus($other) {
                                                        $otherAmount = is_object($other) && method_exists($other, 'getAmount') ? $other->getAmount() : $other;
                                                        return new class($this->amount + $otherAmount) {
                                                            private $amount;
                                                            public function __construct($amount) { $this->amount = $amount; }
                                                            public function getAmount() { return $this->amount; }
                                                        };
                                                    }
                                                };
                                            }
                                            
                                            public function getAmount() { return $this->amount; }
                                            
                                            public function plus($other) {
                                                $otherAmount = is_object($other) && method_exists($other, 'getAmount') ? $other->getAmount() : $other;
                                                return new class($this->amount + $otherAmount) {
                                                    private $amount;
                                                    public function __construct($amount) { $this->amount = $amount; }
                                                    public function getAmount() { return $this->amount; }
                                                };
                                            }
                                        };
                                    }
                                    
                                    public function dividedBy($divisor, $mode = null) {
                                        return new class($this->amount / $divisor) {
                                            private $amount;
                                            public function __construct($amount) { $this->amount = $amount; }
                                            public function getAmount() { return $this->amount; }
                                        };
                                    }
                                    
                                    public function plus($other) {
                                        $otherAmount = is_object($other) && method_exists($other, 'getAmount') ? $other->getAmount() : $other;
                                        return new class($this->amount + $otherAmount) {
                                            private $amount;
                                            public function __construct($amount) { $this->amount = $amount; }
                                            public function getAmount() { return $this->amount; }
                                        };
                                    }
                                    
                                    public function getAmount() { return $this->amount; }
                                };
                            }
                            
                            public function getAmount() { return $this->amount; }
                        };
                    }
                    
                    public function formatMoney($amount) { 
                        if (is_object($amount) && method_exists($amount, 'getAmount')) {
                            return number_format($amount->getAmount(), 2, ',', '.') . ' COP';
                        }
                        if (is_object($amount) && method_exists($amount, 'toFloat')) {
                            return number_format($amount->toFloat(), 2, ',', '.') . ' COP';
                        }
                        return number_format($amount, 2, ',', '.') . ' COP'; 
                    }
                    
                    public function formatPercentage($percentage) {
                        return $percentage . '%';
                    }
                    
                    public function totalAmount() {
                        // Calcular el total del item (precio unitario * cantidad + impuestos)
                        $subtotal = $this->unit_price->getAmount() * $this->quantity;
                        $tax = $subtotal * ($this->tax_percentage / 100);
                        return new class($subtotal + $tax) {
                            private $amount;
                            public function __construct($amount) { $this->amount = $amount; }
                            public function getAmount() { return $this->amount; }
                        };
                    }
                },
            ]);
            
            $this->paymentInstructions = [
                (object) [
                    'name' => 'Transferencia Bancaria',
                    'description' => 'Realice su pago mediante transferencia bancaria',
                    'fields' => [
                        'Banco' => 'Banco de Ejemplo',
                        'Cuenta' => '123-456-789',
                        'Titular' => $companyName ?: 'Fundación Odontológica Zoila Padilla'
                    ],
                    'qrcode' => null
                ]
            ];
        }
        
        public function getTypeLabel() { 
            return 'FACTURA DE VENTA'; 
        }
        
        public function getStateLabel() { 
            return 'draft'; 
        }
        
        public function totalAmount() { 
            return 119000; 
        }
        
        public function totalTaxAmount() {
            // Retornar un mock de Brick\Money que simule un amount positivo
            return new class {
                private $amount = 19000;
                public function getAmount() { return $this->amount; }
                public function isPositive() { return $this->amount > 0; }
            };
        }
        
        public function subTotalAmount() {
            // Retornar el subtotal sin impuestos
            return new class {
                private $amount = 100000;
                public function getAmount() { return $this->amount; }
                public function isPositive() { return $this->amount > 0; }
            };
        }
        
        public function subTotalDiscountedAmount() {
            // Retornar el subtotal con descuentos aplicados (mismo que subtotal si no hay descuentos)
            return new class {
                private $amount = 100000;
                public function getAmount() { return $this->amount; }
                public function isPositive() { return $this->amount > 0; }
            };
        }
        
        public function formatMoney($amount) { 
            if (is_object($amount) && method_exists($amount, 'getAmount')) {
                return number_format($amount->getAmount(), 2, ',', '.') . ' COP';
            }
            if (is_object($amount) && method_exists($amount, 'toFloat')) {
                return number_format($amount->toFloat(), 2, ',', '.') . ' COP';
            }
            return number_format($amount, 2, ',', '.') . ' COP'; 
        }
    };
@endphp

<div class="bg-white rounded-lg shadow-lg border overflow-hidden">
    {{-- Incluir los estilos del template seleccionado --}}
    @if($templateName === 'colombia')
        @include('invoices::colombia.style', ['invoice' => $mockInvoice])
    @else
        @include('invoices::default.style', ['invoice' => $mockInvoice])
    @endif

    {{-- Incluir el template original seleccionado con estilos inline forzados --}}
    <div class="p-4" style="font-family: {{ $font }}; transform: scale(0.75); transform-origin: top left; width: 133.33%; height: auto; --template-color: {{ $color }};">
        <style>
            /* Forzar colores en el preview - Solución específica */
            
            /* Título principal H1 */
            .invoice-preview h1 { color: {{ $color }} !important; }
            
            /* Clases específicas de template */
            .invoice-preview .bg-template-color { background-color: {{ $color }} !important; }
            .invoice-preview .text-template-color { color: {{ $color }} !important; }
            .invoice-preview .border-template-color { border-color: {{ $color }} !important; }
            
            /* Template Colombia - Elementos con background-color sólido */
            .invoice-preview tr[style*="background-color"] { 
                background-color: {{ $color }} !important; 
                color: white !important;
            }
            .invoice-preview p[style*="background-color"]:not([style*="background-color:"][style*="10"]) { 
                background-color: {{ $color }} !important; 
                color: white !important;
            }
            
            /* Template Default - Títulos de secciones (background sólido) */
            .invoice-preview h3[style*="background-color"]:not([style*="10"]) {
                background-color: {{ $color }} !important;
                color: white !important;
            }
            
            /* Template Default - Backgrounds con transparencia (como el total) */
            .invoice-preview [style*="background-color:"][style*="10"] {
                background-color: {{ $color }}10 !important; /* Mantener transparencia */
            }
            
            /* Template Default - Textos con color específico */
            .invoice-preview span[style*="color:"],
            .invoice-preview p[style*="color:"]:not([style*="background-color"]) {
                color: {{ $color }} !important;
            }
            
            /* Template Default - Bordes */
            .invoice-preview [style*="border-color:"] {
                border-color: {{ $color }} !important;
            }
            
            /* República de Colombia en template Colombia */
            .invoice-preview p[style*="color:"][class*="text-xs"] {
                color: {{ $color }} !important;
            }
            
            /* Forzar logo si existe */
            @if($logoUrl)
            .invoice-preview img[alt*="Logo"],
            .invoice-preview img[src*="logo"],
            .invoice-preview img[height="120"],
            .invoice-preview td[width="25%"] img {
                content: url('{{ $logoUrl }}') !important;
                max-width: 150px !important;
                height: 120px !important;
                display: block !important;
                visibility: visible !important;
                margin: 0 auto 8px !important;
            }
            
            /* Asegurar que el contenedor del logo sea visible */
            .invoice-preview td[width="25%"] {
                display: table-cell !important;
            }
            @endif
        </style>
        
        <script>
            // Función para forzar el logo - mejorada para evitar que se quite
            function forceLogoDisplay() {
                @if($logoUrl)
                // Buscar todas las posibles imágenes del logo
                const logoImages = document.querySelectorAll('.invoice-preview img[alt*="Logo"], .invoice-preview img[height="120"], .invoice-preview td[width="25%"] img');
                
                if (logoImages.length > 0) {
                    logoImages.forEach((img) => {
                        // Solo cambiar si no tiene ya el logo correcto
                        if (img.src !== `{{ $logoUrl }}`) {
                            img.src = `{{ $logoUrl }}`;
                            img.style.maxWidth = '150px';
                            img.style.height = '120px';
                            img.style.display = 'block';
                            img.style.visibility = 'visible';
                            img.style.margin = '0 auto 8px';
                        }
                    });
                }
                @endif
            }
            
            // Ejecutar inmediatamente
            forceLogoDisplay();
            
            // Ejecutar cuando el DOM esté listo
            document.addEventListener('DOMContentLoaded', forceLogoDisplay);
            
            // Ejecutar después de cualquier cambio en el DOM (para Filament)
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'childList' || mutation.type === 'attributes') {
                        setTimeout(forceLogoDisplay, 50);
                    }
                });
            });
            
            // Observar cambios en el contenedor de la vista previa
            setTimeout(() => {
                const previewContainer = document.querySelector('.invoice-preview');
                if (previewContainer) {
                    observer.observe(previewContainer, {
                        childList: true,
                        subtree: true,
                        attributes: true,
                        attributeFilter: ['src']
                    });
                }
            }, 100);
        </script>
        
        <div class="invoice-preview">
            @if($templateName === 'colombia')
                @include('invoices::colombia.invoice', ['invoice' => $mockInvoice])
            @else
                @include('invoices::default.invoice', ['invoice' => $mockInvoice])
            @endif
        </div>
    </div>

    {{-- Nota informativa --}}
    <div class="m-4 p-3 bg-blue-50 rounded border-l-4" style="border-color: {{ $color }};">
        <p class="text-sm text-gray-700">
            <span class="font-medium">📋 Vista Previa:</span> 
            Esta es una vista previa del template <strong>{{ $templateName === 'default' ? 'por Defecto' : 'Colombia' }}</strong>. 
            Los cambios de color y tipografía se aplicarán automáticamente cuando guarde la configuración.
        </p>
        <div class="text-xs mt-2 text-gray-500">
            ✅ Color: {{ $color }} | ✅ Fuente: {{ $font }} | {{ $logoUrl ? '✅ Logo configurado' : '❌ Sin logo' }}
        </div>
    </div>
</div>
