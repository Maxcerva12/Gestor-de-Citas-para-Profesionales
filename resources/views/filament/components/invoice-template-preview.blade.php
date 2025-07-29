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
    
    // Debug temporal para ver qué está pasando con el logo
    // dd(['original_logo' => $logo, 'processed_logo_url' => $logoUrl]);
    
    // Si aún no tenemos logo, intentar obtenerlo directamente de InvoiceSettings
    if (!$logoUrl) {
        $logoUrl = \App\Models\InvoiceSettings::getCompanyLogo();
        // Si seguimos sin logo pero hay una configuración guardada, usar Storage::url directamente
        if (!$logoUrl) {
            $savedLogo = \App\Models\InvoiceSettings::get('company_logo');
            if ($savedLogo) {
                $logoUrl = \Storage::url($savedLogo);
            }
        }
    }
    
    // Crear un mock invoice object para la vista previa
    $mockInvoice = (object) [
        'serial_number' => 'FACT-001',
        'templateData' => [
            'color' => $color,
            'font' => $font,
        ],
        'logo' => $logoUrl,
        'created_at' => now(),
        'due_at' => now()->addDays(30),
        'paid_at' => null,
        'fields' => [],
        'seller' => (object) [
            'name' => $companyName ?: 'Mi Empresa',
            'email' => $companyEmail ?: 'email@empresa.com',
            'phone' => $companyPhone ?: '+57 123 456 7890',
            'address' => (object) [
                'street' => 'Calle Ejemplo 123',
                'city' => 'Bogotá',
                'state' => 'Cundinamarca',
                'postal_code' => '110111',
                'country' => 'Colombia',
            ]
        ],
        'buyer' => (object) [
            'name' => 'Cliente de Ejemplo',
            'email' => 'cliente@ejemplo.com',
            'phone' => '+57 987 654 3210',
            'address' => (object) [
                'street' => 'Carrera Ejemplo 456',
                'city' => 'Medellín',
                'state' => 'Antioquia',
                'postal_code' => '050001',
                'country' => 'Colombia',
            ],
            'shipping_address' => null,
        ],
        'items' => collect([
            (object) [
                'title' => 'Servicio de Consultoría',
                'description' => 'Consultoría profesional especializada',
                'quantity' => 2,
                'price_per_unit' => 150000,
                'sub_total_price' => 300000,
            ],
            (object) [
                'title' => 'Desarrollo de Software',
                'description' => 'Desarrollo de aplicación personalizada',
                'quantity' => 1,
                'price_per_unit' => 500000,
                'sub_total_price' => 500000,
            ],
        ]),
    ];
    
    $mockInvoice->getTypeLabel = function() { return 'FACTURA DE VENTA'; };
    $mockInvoice->getStateLabel = function() { return 'Pendiente de Pago'; };
    $mockInvoice->totalAmount = function() { return 952000; };
    $mockInvoice->formatMoney = function($amount) { return '$' . number_format($amount, 0, ',', '.'); };
@endphp

<div class="bg-white rounded-lg shadow-lg border overflow-hidden" style="font-family: {{ $font }};">
    @if($templateName === 'default')
        {{-- Vista previa del template default --}}
        <div class="h-2 w-full" style="background-color: {{ $color }}"></div>
        
        <div class="p-6">
            <div class="mb-8 flex justify-between items-start">
                <div>
                    <h1 class="mb-1 text-2xl font-bold">
                        FACTURA DE VENTA
                    </h1>
                    <p class="mb-5 text-sm text-gray-600">
                        Pendiente de Pago
                    </p>

                    <div class="space-y-1 text-xs">
                        <div class="flex">
                            <span class="font-semibold w-24">Número:</span>
                            <span class="font-bold">FACT-001</span>
                        </div>
                        <div class="flex">
                            <span class="font-semibold w-24">Fecha:</span>
                            <span>{{ now()->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex">
                            <span class="font-semibold w-24">Vencimiento:</span>
                            <span>{{ now()->addDays(30)->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
                @if($logoUrl)
                <div class="w-20 h-20">
                    <img src="{{ $logoUrl }}" alt="Logo" class="w-full h-full object-contain" onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'text-xs text-red-500\'>Error cargando logo<br>{{ $logoUrl }}</div>';">
                </div>
                @else
                <div class="w-20 h-20 bg-gray-100 flex items-center justify-center text-xs text-gray-500">
                    Sin Logo
                </div>
                @endif
            </div>

            <div class="mb-6 grid grid-cols-2 gap-6">
                <div>
                    <p class="mb-1 pb-1 text-xs text-gray-500 font-semibold">DE:</p>
                    <div>
                        <p class="font-semibold">{{ $companyName ?: 'Mi Empresa' }}</p>
                        <p class="text-sm">{{ $companyEmail ?: 'email@empresa.com' }}</p>
                        <p class="text-sm">{{ $companyPhone ?: '+57 123 456 7890' }}</p>
                        <p class="text-sm">Calle Ejemplo 123</p>
                        <p class="text-sm">Bogotá, Colombia</p>
                    </div>
                </div>
                <div>
                    <p class="mb-1 pb-1 text-xs text-gray-500 font-semibold">PARA:</p>
                    <div>
                        <p class="font-semibold">Cliente de Ejemplo</p>
                        <p class="text-sm">cliente@ejemplo.com</p>
                        <p class="text-sm">+57 987 654 3210</p>
                        <p class="text-sm">Carrera Ejemplo 456</p>
                        <p class="text-sm">Medellín, Colombia</p>
                    </div>
                </div>
            </div>

            {{-- Tabla de servicios --}}
            <div class="mb-6">
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="text-white" style="background-color: {{ $color }};">
                            <th class="border border-gray-300 p-2 text-left">Descripción</th>
                            <th class="border border-gray-300 p-2 text-center">Cant.</th>
                            <th class="border border-gray-300 p-2 text-right">Precio Unit.</th>
                            <th class="border border-gray-300 p-2 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-gray-300 p-2">
                                <div class="font-medium">Servicio de Consultoría</div>
                                <div class="text-sm text-gray-600">Consultoría profesional</div>
                            </td>
                            <td class="border border-gray-300 p-2 text-center">2</td>
                            <td class="border border-gray-300 p-2 text-right">$150.000</td>
                            <td class="border border-gray-300 p-2 text-right font-medium">$300.000</td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 p-2">
                                <div class="font-medium">Desarrollo de Software</div>
                                <div class="text-sm text-gray-600">Aplicación personalizada</div>
                            </td>
                            <td class="border border-gray-300 p-2 text-center">1</td>
                            <td class="border border-gray-300 p-2 text-right">$500.000</td>
                            <td class="border border-gray-300 p-2 text-right font-medium">$500.000</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Totales --}}
            <div class="flex justify-end">
                <div class="w-1/3">
                    <div class="bg-gray-50 p-4 rounded border">
                        <div class="flex justify-between py-1">
                            <span>Subtotal:</span>
                            <span>$800.000</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span>IVA (19%):</span>
                            <span>$152.000</span>
                        </div>
                        <hr class="my-2">
                        <div class="flex justify-between py-1 font-bold text-lg" style="color: {{ $color }};">
                            <span>Total:</span>
                            <span>$952.000</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 p-3 text-xs text-gray-500 text-center border-t">
            FACT-001 • $952.000 | Página 1
        </div>

    @else
        {{-- Vista previa del template colombia --}}
        <div class="flex">
            <div class="h-2 flex-1 bg-yellow-400"></div>
            <div class="h-2 flex-1 bg-blue-600"></div>
            <div class="h-2 flex-1 bg-red-600"></div>
        </div>
        
        <div class="p-6">
            <div class="mb-8 flex justify-between items-start">
                <div>
                    <h1 class="mb-1 text-3xl font-bold" style="color: {{ $color }}">
                        FACTURA DE VENTA
                    </h1>
                    <p class="mb-2 text-sm font-semibold text-gray-600">
                        Pendiente de Pago
                    </p>

                    <div class="border border-gray-300 bg-gray-50">
                        <div class="text-xs border-b border-gray-300 flex">
                            <div class="py-1 px-2 font-semibold w-1/3">Número de Factura:</div>
                            <div class="py-1 px-2 font-bold flex-1">FACT-001</div>
                        </div>
                        <div class="text-xs border-b border-gray-300 flex">
                            <div class="py-1 px-2 font-semibold w-1/3">Fecha de Emisión:</div>
                            <div class="py-1 px-2 flex-1">{{ now()->format('d/m/Y') }}</div>
                        </div>
                        <div class="text-xs border-b border-gray-300 flex">
                            <div class="py-1 px-2 font-semibold w-1/3">Fecha de Vencimiento:</div>
                            <div class="py-1 px-2 flex-1">{{ now()->addDays(30)->format('d/m/Y') }}</div>
                        </div>
                        <div class="text-xs flex">
                            <div class="py-1 px-2 font-semibold w-1/3">Moneda:</div>
                            <div class="py-1 px-2 flex-1">Peso Colombiano (COP)</div>
                        </div>
                    </div>
                </div>
                @if($logoUrl)
                <div class="w-24 text-center">
                    <img src="{{ $logoUrl }}" alt="Logo de la empresa" class="mx-auto mb-2 max-h-20" onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'text-xs text-red-500\'>Error cargando logo<br>{{ $logoUrl }}</div>';">
                    <p class="text-xs font-semibold" style="color: {{ $color }}">
                        República de Colombia
                    </p>
                </div>
                @else
                <div class="w-24 text-center">
                    <div class="mx-auto mb-2 max-h-20 bg-gray-100 flex items-center justify-center text-xs text-gray-500 h-20">
                        Sin Logo
                    </div>
                    <p class="text-xs font-semibold" style="color: {{ $color }}">
                        República de Colombia
                    </p>
                </div>
                @endif
            </div>

            <div class="mb-6 grid grid-cols-2 gap-4">
                <div class="border border-gray-300 p-3">
                    <p class="mb-2 text-sm font-bold text-white px-2 py-1" style="background-color: {{ $color }}">
                        DATOS DEL EMISOR
                    </p>
                    <div>
                        <p class="font-semibold text-sm">{{ $companyName ?: 'Mi Empresa' }}</p>
                        <p class="text-xs">{{ $companyEmail ?: 'email@empresa.com' }}</p>
                        <p class="text-xs">{{ $companyPhone ?: '+57 123 456 7890' }}</p>
                        <p class="text-xs">Calle Ejemplo 123</p>
                        <p class="text-xs">Bogotá, Cundinamarca</p>
                        <p class="text-xs">Colombia - 110111</p>
                    </div>
                </div>
                <div class="border border-gray-300 p-3">
                    <p class="mb-2 text-sm font-bold text-white px-2 py-1" style="background-color: {{ $color }}">
                        DATOS DEL RECEPTOR
                    </p>
                    <div>
                        <p class="font-semibold text-sm">Cliente de Ejemplo</p>
                        <p class="text-xs">cliente@ejemplo.com</p>
                        <p class="text-xs">+57 987 654 3210</p>
                        <p class="text-xs">Carrera Ejemplo 456</p>
                        <p class="text-xs">Medellín, Antioquia</p>
                        <p class="text-xs">Colombia - 050001</p>
                    </div>
                </div>
            </div>

            {{-- Tabla de servicios estilo Colombia --}}
            <div class="mb-6">
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="text-white" style="background-color: {{ $color }};">
                            <th class="border border-gray-300 p-2 text-left">Descripción del Servicio</th>
                            <th class="border border-gray-300 p-2 text-center">Cantidad</th>
                            <th class="border border-gray-300 p-2 text-right">Valor Unitario</th>
                            <th class="border border-gray-300 p-2 text-right">Valor Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-300">
                            <td class="border border-gray-300 p-2">
                                <div class="font-medium">Servicio de Consultoría Profesional</div>
                                <div class="text-xs text-gray-600">Consultoría especializada en el sector</div>
                            </td>
                            <td class="border border-gray-300 p-2 text-center">2</td>
                            <td class="border border-gray-300 p-2 text-right">$150.000</td>
                            <td class="border border-gray-300 p-2 text-right font-medium">$300.000</td>
                        </tr>
                        <tr class="border-b border-gray-300">
                            <td class="border border-gray-300 p-2">
                                <div class="font-medium">Desarrollo de Software Personalizado</div>
                                <div class="text-xs text-gray-600">Aplicación web a medida</div>
                            </td>
                            <td class="border border-gray-300 p-2 text-center">1</td>
                            <td class="border border-gray-300 p-2 text-right">$500.000</td>
                            <td class="border border-gray-300 p-2 text-right font-medium">$500.000</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Totales estilo Colombia --}}
            <div class="flex justify-end">
                <div class="w-1/3 border border-gray-300">
                    <div class="text-white px-2 py-1 text-sm font-bold" style="background-color: {{ $color }};">
                        RESUMEN DE FACTURACIÓN
                    </div>
                    <div class="p-3 space-y-1">
                        <div class="flex justify-between text-sm">
                            <span>Subtotal:</span>
                            <span>$800.000</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span>IVA (19%):</span>
                            <span>$152.000</span>
                        </div>
                        <hr class="my-2">
                        <div class="flex justify-between font-bold text-lg" style="color: {{ $color }};">
                            <span>TOTAL A PAGAR:</span>
                            <span>$952.000</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer legal estilo Colombia --}}
            <div class="mt-6 p-3 bg-gray-100 border border-gray-300 text-xs text-gray-600 text-center">
                <p class="font-semibold">Esta factura es válida según la normativa colombiana - DIAN</p>
                <p>Régimen Común - Responsable del IVA - Autorretenedor</p>
            </div>
        </div>

        <div class="bg-gray-50 p-3 text-xs text-gray-500 text-center border-t flex justify-between">
            <span>FACT-001 • $952.000</span>
            <span>Esta factura es válida según la normativa colombiana - DIAN</span>
            <span>Página 1</span>
        </div>
    @endif

    {{-- Nota informativa --}}
    <div class="m-4 p-3 bg-blue-50 rounded border-l-4" style="border-color: {{ $color }};">
        <p class="text-sm text-gray-700">
            <span class="font-medium">📋 Vista Previa:</span> 
            Esta es una vista previa del template <strong>{{ $templateName === 'default' ? 'por Defecto' : 'Colombia' }}</strong>. 
            Los cambios de color y tipografía se aplicarán automáticamente cuando guarde la configuración.
        </p>
    </div>
</div>
