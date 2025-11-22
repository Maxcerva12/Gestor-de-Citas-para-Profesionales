<div class="colombia-invoice">
    <table class="mb-6 w-full">
        <tbody>
            <tr>
                @if ($invoice->logo)
                    <td class="p-0" width="25%" style="vertical-align: middle;">
                        <div style="text-align: center;">
                            <img src="{{ $invoice->logo }}" alt="Logo" style="max-width: 200px; height: auto; display: block; margin: 0 auto;">
                        </div>
                    </td>
                @endif
                <td class="p-0" style="vertical-align: middle; text-align: center;">
                    <h1 class="mb-1 text-2xl font-bold" style="color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}">
                        FACTURA ELECTRÓNICA DE VENTA
                    </h1>
                    <p class="text-xs font-semibold mb-2" style="color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}">
                        República de Colombia
                    </p>
                    <p class="text-xs font-semibold text-gray-600">
                        Estado: {{ $invoice->getStateLabel() }}
                    </p>
                </td>
            </tr>
        </tbody>
    </table>

    <table class="mb-6 w-full border border-gray-300">
        <tbody class="bg-gray-50">
            <tr class="text-xs border-b">
                <td class="whitespace-nowrap pr-2 py-2 px-3 font-bold text-gray-900">
                    Número de Factura:
                </td>
                <td class="py-2 px-3 font-bold text-gray-900" width="100%">
                    {{ $invoice->serial_number }}
                </td>
            </tr>
            <tr class="text-xs border-b">
                <td class="whitespace-nowrap pr-2 py-2 px-3 font-semibold text-gray-900">
                    Fecha de Emisión:
                </td>
                <td class="py-2 px-3 text-gray-900" width="100%">
                    {{ $invoice->created_at?->format('d/m/Y') }}
                </td>
            </tr>
            @if ($invoice->due_at)
                <tr class="text-xs border-b">
                    <td class="whitespace-nowrap pr-2 py-2 px-3 font-semibold text-gray-900">
                        Fecha de Vencimiento:
                    </td>
                    <td class="py-2 px-3 text-gray-900" width="100%">
                        {{ $invoice->due_at->format('d/m/Y') }}
                    </td>
                </tr>
            @endif
            @if ($invoice->paid_at)
                <tr class="text-xs border-b">
                    <td class="whitespace-nowrap pr-2 py-2 px-3 font-semibold text-gray-900">
                        Fecha de Pago:
                    </td>
                    <td class="py-2 px-3 text-gray-900" width="100%">
                        {{ $invoice->paid_at->format('d/m/Y H:i') }}
                    </td>
                </tr>
            @endif
            <tr class="text-xs border-b">
                <td class="whitespace-nowrap pr-2 py-2 px-3 font-semibold text-gray-900">
                    Moneda:
                </td>
                <td class="py-2 px-3 text-gray-900" width="100%">
                    Peso Colombiano (COP)
                </td>
            </tr>
            @foreach ($invoice->fields as $key => $value)
                <tr class="text-xs border-b">
                    <td class="whitespace-nowrap pr-2 py-2 px-3 font-semibold text-gray-900">
                        {{ $key }}:
                    </td>
                    <td class="py-2 px-3 text-gray-900" width="100%">
                        {{ $value }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="mb-6 w-full">
        <tbody>
            <tr>
                <td class="align-top border border-gray-300 p-4 text-gray-900" width="50%">
                    <p class="mb-3 text-sm font-bold text-white px-3 py-2" style="background-color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}">
                        INFORMACIÓN DEL EMISOR
                    </p>
                    @include('invoices::colombia.includes.party', [
                        'party' => $invoice->seller,
                        'isEmitter' => true,
                    ])
                </td>

                <td class="align-top border border-gray-300 p-4 text-gray-900" width="50%">
                    <p class="mb-3 text-sm font-bold text-white px-3 py-2" style="background-color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}">
                        INFORMACIÓN DEL ADQUIRIENTE
                    </p>
                    @include('invoices::colombia.includes.party', [
                        'party' => $invoice->buyer,
                        'isEmitter' => false,
                    ])
                </td>
            </tr>
        </tbody>
    </table>

    @if ($invoice->description)
        <div class="mb-6 p-4 border border-gray-300 bg-gray-50">
            <p class="text-sm font-bold mb-2 text-gray-900">Observaciones:</p>
            <p class="text-sm text-gray-900">{{ $invoice->description }}</p>
        </div>
    @endif

    <div class="mb-6">
        <table class="w-full border-collapse border border-gray-400">
            <thead>
                <tr class="text-white text-xs font-semibold" style="background-color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}">
                    <th class="border border-gray-400 p-3 text-left">DESCRIPCIÓN</th>
                    <th class="border border-gray-400 p-3 text-center" width="8%">CANT.</th>
                    <th class="border border-gray-400 p-3 text-right" width="12%">VALOR UNITARIO</th>
                    <th class="border border-gray-400 p-3 text-center" width="8%">IVA %</th>
                    <th class="border border-gray-400 p-3 text-right" width="12%">VALOR IVA</th>
                    <th class="border border-gray-400 p-3 text-right" width="12%">SUBTOTAL</th>
                    <th class="border border-gray-400 p-3 text-right" width="12%">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $item)
                    @php
                        $subtotal = $item->unit_price->multipliedBy($item->quantity);
                        $taxRate = $item->tax_percentage ?? 19;
                        $taxAmount = $subtotal->multipliedBy($taxRate)->dividedBy(100, Brick\Math\RoundingMode::HALF_UP);
                        $total = $subtotal->plus($taxAmount);
                    @endphp
                    <tr class="text-xs {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                        <td class="border border-gray-300 p-3 text-gray-900">
                            <strong class="text-gray-900">{{ $item->label }}</strong>
                            @if ($item->description)
                                <br><span class="text-gray-700 text-xs">{{ $item->description }}</span>
                            @endif
                        </td>
                        <td class="border border-gray-300 p-3 text-center font-semibold text-gray-900">{{ $item->quantity }}</td>
                        <td class="border border-gray-300 p-3 text-right text-gray-900">{{ $invoice->formatMoney($item->unit_price) }}</td>
                        <td class="border border-gray-300 p-3 text-center text-gray-900">{{ number_format($taxRate, 1) }}%</td>
                        <td class="border border-gray-300 p-3 text-right text-gray-900">{{ $invoice->formatMoney($taxAmount) }}</td>
                        <td class="border border-gray-300 p-3 text-right text-gray-900">{{ $invoice->formatMoney($subtotal) }}</td>
                        <td class="border border-gray-300 p-3 text-right font-bold text-gray-900">{{ $invoice->formatMoney($total) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mb-6">
        @php
            use App\Models\InvoiceSettings;
            
            // Calcular subtotal de items
            $subtotal = collect($invoice->items)->reduce(function ($carry, $item) {
                // Validar que existan los objetos necesarios
                if (!$item || !isset($item->unit_price) || !is_object($item->unit_price)) {
                    return $carry;
                }
                
                try {
                    $itemTotal = $item->unit_price->multipliedBy($item->quantity);
                    return $carry ? $carry->plus($itemTotal) : $itemTotal;
                } catch (\Exception $e) {
                    return $carry;
                }
            }, null) ?: \Brick\Money\Money::of(0, 'COP');
            
            // Usar el descuento guardado en la factura (no el de configuración global)
            // Esto asegura que las facturas mantengan el descuento que tenían al momento de creación
            
            // Primero verificar si el objeto $invoice ya tiene las propiedades (como en el preview)
            if (isset($invoice->discount_enabled) && isset($invoice->discount_percentage)) {
                $discountEnabled = $invoice->discount_enabled ? true : false;
                $discountPercentage = $discountEnabled ? (float) ($invoice->discount_percentage ?? 0) : 0;
            } else {
                // Si no, obtener el modelo completo de la factura desde la base de datos usando el serial_number
                $invoiceModel = \App\Models\Invoice::where('serial_number', $invoice->serial_number)->first();
                $discountEnabled = ($invoiceModel && $invoiceModel->discount_enabled) ? true : false;
                $discountPercentage = $discountEnabled ? (float) ($invoiceModel->discount_percentage ?? 0) : 0;
            }
            
            // Calcular descuento si está habilitado
            $discountAmount = \Brick\Money\Money::of(0, 'COP');
            $subtotalAfterDiscount = $subtotal;
            
            if ($discountEnabled && $discountPercentage > 0 && $subtotal instanceof \Brick\Money\Money) {
                try {
                    // Calcular monto de descuento (porcentaje sobre subtotal)
                    $discountAmount = $subtotal->multipliedBy($discountPercentage)->dividedBy(100, \Brick\Math\RoundingMode::HALF_UP);
                    // Calcular subtotal después de descuento (100% - porcentaje descuento)
                    $subtotalAfterDiscount = $subtotal->multipliedBy(100 - $discountPercentage)->dividedBy(100, \Brick\Math\RoundingMode::HALF_UP);
                } catch (\Exception $e) {
                    // Si hay error, mantener subtotal original
                    $discountAmount = \Brick\Money\Money::of(0, 'COP');
                    $subtotalAfterDiscount = $subtotal;
                }
            }
            
            // Calcular IVA sobre el subtotal con descuento
            $totalTax = collect($invoice->items)->reduce(function ($carry, $item) use ($discountPercentage, $discountEnabled) {
                // Validar que existan los objetos necesarios
                if (!$item || !isset($item->unit_price) || !is_object($item->unit_price)) {
                    return $carry;
                }
                
                try {
                    $itemSubtotal = $item->unit_price->multipliedBy($item->quantity);
                    
                    // Aplicar descuento al item si está habilitado
                    if ($discountEnabled && $discountPercentage > 0) {
                        // Aplicar directamente el porcentaje reducido (100% - descuento%)
                        $itemSubtotal = $itemSubtotal->multipliedBy(100 - $discountPercentage)->dividedBy(100, \Brick\Math\RoundingMode::HALF_UP);
                    }
                    
                    $itemTax = $itemSubtotal->multipliedBy($item->tax_percentage ?? 19)->dividedBy(100, \Brick\Math\RoundingMode::HALF_UP);
                    return $carry ? $carry->plus($itemTax) : $itemTax;
                } catch (\Exception $e) {
                    return $carry;
                }
            }, null) ?: \Brick\Money\Money::of(0, 'COP');
            
            $total = $subtotalAfterDiscount->plus($totalTax);
        @endphp
    
        <table class="ml-auto border border-gray-300" width="45%">
            <tbody class="bg-gray-50">
                <tr class="text-sm border-b border-gray-300">
                    <td class="py-2 px-4 text-right font-semibold text-gray-900">Subtotal:</td>
                    <td class="py-2 px-4 text-right font-semibold text-gray-900">{{ $invoice->formatMoney($subtotal) }}</td>
                </tr>
                
                @if ($discountEnabled && $discountPercentage > 0)
                    <tr class="text-sm border-b border-gray-300 bg-green-50">
                        <td class="py-2 px-4 text-right font-semibold text-green-800">
                            Descuento ({{ number_format($discountPercentage, 2) }}%):
                        </td>
                        <td class="py-2 px-4 text-right font-semibold text-green-800">
                            -{{ $invoice->formatMoney($discountAmount) }}
                        </td>
                    </tr>
                    
                    <tr class="text-sm border-b border-gray-300">
                        <td class="py-2 px-4 text-right font-semibold text-gray-900">Subtotal con Descuento:</td>
                        <td class="py-2 px-4 text-right font-semibold text-gray-900">{{ $invoice->formatMoney($subtotalAfterDiscount) }}</td>
                    </tr>
                @endif
                
                @php
                    $taxAmount = (float) str_replace(',', '', $totalTax->getAmount());
                @endphp
                @if ($taxAmount > 0)
                    <tr class="text-sm border-b border-gray-300">
                        <td class="py-2 px-4 text-right font-semibold text-gray-900">
                            IVA ({{ number_format(
                                collect($invoice->items)
                                    ->pluck('tax_percentage')
                                    ->filter()
                                    ->unique()
                                    ->first()
                                ?: 19, 1
                            ) }}%):
                        </td>
                        <td class="py-2 px-4 text-right font-semibold text-gray-900">{{ $invoice->formatMoney($totalTax) }}</td>
                    </tr>
                @endif
                
                <tr class="text-lg font-bold" style="color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}">
                    <td class="py-3 px-4 text-right border-t-2 border-gray-400 text-gray-900">TOTAL A PAGAR:</td>
                    <td class="py-3 px-4 text-right border-t-2 border-gray-400 text-gray-900">{{ $invoice->formatMoney($total) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    @if ($invoice->paymentInstructions && count($invoice->paymentInstructions) > 0)
        <div class="mb-6">
            <h3 class="text-base font-bold mb-3 px-3 py-2 text-white" style="background-color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}">
                MEDIO DE PAGO / INSTRUCCIONES DE PAGO
            </h3>
            @foreach ($invoice->paymentInstructions as $instruction)
                <div class="border border-gray-300 p-4 mb-3 bg-gray-50">
                    <h4 class="font-bold text-sm mb-2 text-gray-900">{{ $instruction->name }}</h4>
                    @if ($instruction->description)
                        <p class="text-xs text-gray-900 mb-3">{{ $instruction->description }}</p>
                    @endif
                    
                    <div class="flex">
                        <div class="flex-1">
                            @if ($instruction->fields)
                                <table class="text-xs w-full">
                                    @foreach ($instruction->fields as $key => $value)
                                        <tr class="border-b border-gray-200">
                                            <td class="py-1 pr-3 font-semibold text-gray-900">{{ $key }}:</td>
                                            <td class="py-1 text-gray-900">{!! $value !!}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            @endif
                        </div>
                        
                        @if ($instruction->qrcode)
                            <div class="ml-4">
                                <img src="{{ $instruction->qrcode }}" alt="Código QR para pago" width="80" height="80">
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="mt-8 pt-4 border-t-2 border-gray-400">
        <div class="text-center mb-4">
            <p class="font-bold text-gray-900 text-sm mb-2">Este documento es una representación impresa de la factura electrónica</p>
            <p class="text-xs text-gray-700 mb-3">
                Válida según el Decreto 1625 de 2016 y demás normas vigentes sobre facturación electrónica en Colombia
            </p>
        </div>
        
        <div class="bg-gray-50 border border-gray-300 p-3 text-xs text-gray-700">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <p><strong>NIT Emisor:</strong> {{ $invoice->seller->tax_number ?? 'N/A' }}</p>
                    <p><strong>Razón Social:</strong> {{ $invoice->seller->company ?? $invoice->seller->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p><strong>Régimen Tributario:</strong> {{ data_get($invoice->seller->fields, 'Régimen', 'Común') }}</p>
                    <p><strong>Responsabilidades Fiscales:</strong> {{ data_get($invoice->seller->fields, 'Responsabilidades Fiscales', 'N/A') }}</p>
                </div>
            </div>
            @if (data_get($invoice->fields, 'Resolución'))
                <div class="mt-3 pt-3 border-t border-gray-300">
                    <p><strong>Resolución de Facturación:</strong> {{ data_get($invoice->fields, 'Resolución') }}</p>
                    @if (data_get($invoice->fields, 'Rango Autorizado'))
                        <p><strong>Rango Autorizado:</strong> {{ data_get($invoice->fields, 'Rango Autorizado') }}</p>
                    @endif
                    @if (data_get($invoice->fields, 'Vigencia'))
                        <p><strong>Vigencia:</strong> {{ data_get($invoice->fields, 'Vigencia') }}</p>
                    @endif
                </div>
            @endif
        </div>
        
        <div class="text-center mt-4 text-xs text-gray-600">
            <p>Para consultas sobre esta factura, contacte con el emisor</p>
        </div>
    </div>
</div>
