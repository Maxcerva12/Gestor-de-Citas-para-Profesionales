<div class="bg-white min-h-screen">
    <div class="border-b-2 mb-6" style="border-color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}">
        <div class="flex justify-between items-start py-6">
            @if ($invoice->logo)
                <div class="flex-shrink-0 mr-6">
                    <img src="{{ $invoice->logo }}" alt="Logo" class="h-24 w-auto">
                </div>
            @endif
            <div class="flex-1">
                <h1 class="text-4xl font-bold mb-2" style="color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}">
                    FACTURA DE VENTA
                </h1>
                <div class="text-sm text-gray-600 mb-2">
                    Estado: <span class="font-semibold px-2 py-1 rounded text-white text-xs" style="background-color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}">
                        {{ $invoice->getStateLabel() }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="bg-gray-50 p-6 rounded border border-gray-300">
            <h2 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">DATOS DE LA FACTURA</h2>
            <div class="space-y-3">
                <div class="grid grid-cols-3 gap-2">
                    <span class="font-semibold text-gray-700">No. Factura:</span>
                    <span class="col-span-2 font-bold" style="color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}">
                        {{ $invoice->serial_number }}
                    </span>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <span class="font-semibold text-gray-700">Fecha de Emisión:</span>
                    <span class="col-span-2 text-gray-800">
                        {{ $invoice->created_at?->format(config('invoices.date_format')) }}
                    </span>
                </div>
                @if ($invoice->due_at)
                    <div class="grid grid-cols-3 gap-2">
                        <span class="font-semibold text-gray-700">Fecha de Vencimiento:</span>
                        <span class="col-span-2 text-gray-800">
                            {{ $invoice->due_at->format(config('invoices.date_format')) }}
                        </span>
                    </div>
                @endif
                @if ($invoice->paid_at)
                    <div class="grid grid-cols-3 gap-2">
                        <span class="font-semibold text-gray-700">Fecha de Pago:</span>
                        <span class="col-span-2 text-green-600 font-semibold">
                            {{ $invoice->paid_at->format(config('invoices.date_format')) }}
                        </span>
                    </div>
                @endif
                <div class="grid grid-cols-3 gap-2">
                    <span class="font-semibold text-gray-700">Moneda:</span>
                    <span class="col-span-2 text-gray-800">
                        Peso Colombiano (COP)
                    </span>
                </div>
            </div>
        </div>

        @if ($invoice->fields && count($invoice->fields) > 0)
            <div class="bg-gray-50 p-6 rounded border border-gray-300">
                <h2 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">INFORMACIÓN ADICIONAL</h2>
                <div class="space-y-3">
                    @foreach ($invoice->fields as $key => $value)
                        <div class="grid grid-cols-3 gap-2">
                            <span class="font-semibold text-gray-700">{{ $key }}:</span>
                            <span class="col-span-2 text-gray-800">{{ $value }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="border border-gray-300 rounded p-6">
            <h3 class="text-lg font-bold mb-4 text-white px-4 py-2 rounded-t" style="background-color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}; margin: -24px -24px 16px -24px;">
                INFORMACIÓN DEL EMISOR
            </h3>
            <div class="text-sm text-gray-700 leading-relaxed">
                @include('invoices::default.includes.party', ['party' => $invoice->seller, 'isEmitter' => true])
            </div>
        </div>

        <div class="border border-gray-300 rounded p-6">
            <h3 class="text-lg font-bold mb-4 text-white px-4 py-2 rounded-t" style="background-color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}; margin: -24px -24px 16px -24px;">
                INFORMACIÓN DEL ADQUIRIENTE
            </h3>
            <div class="text-sm text-gray-700 leading-relaxed">
                @include('invoices::default.includes.party', ['party' => $invoice->buyer, 'isEmitter' => false])
            </div>
        </div>
    </div>

    @if ($invoice->buyer->shipping_address)
        <div class="border border-gray-300 rounded p-6 mb-8">
            <h3 class="text-lg font-bold mb-4 text-white px-4 py-2 rounded-t" style="background-color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}; margin: -24px -24px 16px -24px;">
                DIRECCIÓN DE ENVÍO
            </h3>
            <div class="text-sm text-gray-700 leading-relaxed">
                @include('invoices::default.includes.address', ['address' => $invoice->buyer->shipping_address])
            </div>
        </div>
    @endif

    @php
        $hasTaxes = $invoice->tax_label || $invoice->totalTaxAmount()->isPositive();
    @endphp

    <div class="mb-8">
        <div class="border border-gray-300 rounded overflow-hidden">
            <div class="text-white px-6 py-4" style="background-color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}">
                <h2 class="text-xl font-bold">DETALLE DE SERVICIOS / PRODUCTOS</h2>
            </div>
            
            <div class="bg-white">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b-2 border-gray-300">
                        <tr>
                            <th class="px-6 py-4 text-left font-bold text-gray-800 text-sm uppercase tracking-wide">
                                Descripción Clara del Servicio/Producto
                            </th>
                            <th class="px-4 py-4 text-center font-bold text-gray-800 text-sm uppercase tracking-wide">
                                Cantidad
                            </th>
                            <th class="px-4 py-4 text-right font-bold text-gray-800 text-sm uppercase tracking-wide">
                                Valor Unitario
                            </th>
                            @if ($hasTaxes)
                                <th class="px-4 py-4 text-right font-bold text-gray-800 text-sm uppercase tracking-wide">
                                    IVA
                                </th>
                            @endif
                            <th class="px-6 py-4 text-right font-bold text-gray-800 text-sm uppercase tracking-wide">
                                Valor Total
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($invoice->items as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900">{{ $item->label }}</div>
                                    @if ($item->description)
                                        <div class="text-sm text-gray-600 mt-1">{{ $item->description }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="bg-gray-100 px-3 py-1 rounded-full text-sm font-semibold text-gray-800">
                                        {{ $item->quantity }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-right font-medium text-gray-800">
                                    {{ $item->formatMoney($item->unit_price) }}
                                </td>
                                @if ($hasTaxes)
                                    <td class="px-4 py-4 text-right text-gray-700">
                                        @if ($item->unit_tax !== null && $item->tax_percentage !== null)
                                            <div class="text-right">
                                                <div class="font-medium">{{ $item->formatMoney($item->unit_tax) }}</div>
                                                <div class="text-xs text-gray-500">({{ $item->formatPercentage($item->tax_percentage) }})</div>
                                            </div>
                                        @elseif ($item->unit_tax !== null)
                                            <span class="font-medium">{{ $item->formatMoney($item->unit_tax) }}</span>
                                        @elseif($item->tax_percentage !== null)
                                            <span class="font-medium">{{ $item->formatPercentage($item->tax_percentage) }}</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                @endif
                                <td class="px-6 py-4 text-right font-bold text-gray-900">
                                    {{ $item->formatMoney($item->totalAmount()) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @php
                use App\Models\InvoiceSettings;
                
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
                $subtotal = $invoice->subTotalAmount();
                $discountAmount = \Brick\Money\Money::of(0, $subtotal->getCurrency()->getCurrencyCode());
                $subtotalAfterDiscount = $subtotal;
                
                if ($discountEnabled && $discountPercentage > 0) {
                    // Calcular monto de descuento
                    $discountAmount = $subtotal->multipliedBy($discountPercentage)->dividedBy(100, \Brick\Math\RoundingMode::HALF_UP);
                    // Calcular subtotal después de descuento
                    $subtotalAfterDiscount = $subtotal->multipliedBy(100 - $discountPercentage)->dividedBy(100, \Brick\Math\RoundingMode::HALF_UP);
                }
                
                // Recalcular impuestos sobre el subtotal con descuento
                $totalTax = collect($invoice->items)->reduce(function ($carry, $item) use ($discountPercentage, $discountEnabled) {
                    $itemSubtotal = $item->unit_price->multipliedBy($item->quantity);
                    
                    // Aplicar descuento al item si está habilitado
                    if ($discountEnabled && $discountPercentage > 0) {
                        // Aplicar directamente el porcentaje reducido
                        $itemSubtotal = $itemSubtotal->multipliedBy(100 - $discountPercentage)->dividedBy(100, \Brick\Math\RoundingMode::HALF_UP);
                    }
                    
                    $itemTax = $itemSubtotal->multipliedBy($item->tax_percentage ?? 0)->dividedBy(100, \Brick\Math\RoundingMode::HALF_UP);
                    return $carry ? $carry->plus($itemTax) : $itemTax;
                }, null) ?: \Brick\Money\Money::of(0, $subtotal->getCurrency()->getCurrencyCode());
                
                $finalTotal = $subtotalAfterDiscount->plus($totalTax);
            @endphp

            <div class="bg-gray-50 border-t-2 border-gray-300">
                <div class="px-6 py-6">
                    <div class="max-w-md ml-auto space-y-3">
                        <div class="flex justify-between py-2 border-b border-gray-300">
                            <span class="font-semibold text-gray-700 text-base">Subtotal:</span>
                            <span class="font-bold text-gray-900 text-base">{{ $invoice->formatMoney($subtotal) }}</span>
                        </div>

                        @if ($discountEnabled && $discountPercentage > 0)
                            <div class="flex justify-between py-2 bg-green-50 px-3 rounded">
                                <span class="text-green-700 font-semibold text-sm">
                                    Descuento ({{ number_format($discountPercentage, 2) }}%):
                                </span>
                                <span class="font-semibold text-green-700 text-sm">
                                    -{{ $invoice->formatMoney($discountAmount) }}
                                </span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-300">
                                <span class="font-semibold text-gray-700 text-base">Subtotal con Descuento:</span>
                                <span class="font-bold text-gray-900 text-base">{{ $invoice->formatMoney($subtotalAfterDiscount) }}</span>
                            </div>
                        @endif

                        @php
                            $taxAmountDefault = (float) str_replace(',', '', $totalTax->getAmount());
                        @endphp
                        @if ($hasTaxes && $taxAmountDefault > 0)
                            <div class="flex justify-between py-2 border-b border-gray-300">
                                <span class="font-semibold text-gray-700 text-base">
                                    IVA ({{ 
                                        number_format(
                                            collect($invoice->items)
                                                ->pluck('tax_percentage')
                                                ->filter()
                                                ->unique()
                                                ->first() ?: 19, 1
                                        ) 
                                    }}%):
                                </span>
                                <span class="font-bold text-gray-900 text-base">{{ $invoice->formatMoney($totalTax) }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between py-4 border-2 rounded px-4" style="border-color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}; background-color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}10">
                            <span class="text-xl font-bold" style="color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}">TOTAL A PAGAR:</span>
                            <span class="text-xl font-bold" style="color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}">
                                {{ $invoice->formatMoney($finalTotal) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($invoice->description)
        <div class="mb-8">
            <div class="border border-gray-300 rounded overflow-hidden">
                <div class="text-white px-6 py-4" style="background-color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}">
                    <h3 class="text-lg font-bold">OBSERVACIONES Y NOTAS</h3>
                </div>
                <div class="p-6 bg-white">
                    <div class="text-gray-700 leading-relaxed whitespace-pre-line">
                        {!! $invoice->description !!}
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($invoice->paymentInstructions)
        <div class="mb-8">
            <div class="border border-gray-300 rounded overflow-hidden">
                <div class="text-white px-6 py-4" style="background-color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}">
                    <h3 class="text-lg font-bold">CONDICIONES DE PAGO / MEDIO DE PAGO</h3>
                </div>
                <div class="bg-white">
                    @foreach ($invoice->paymentInstructions as $paymentInstruction)
                        <div class="p-6 {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    @if ($paymentInstruction->name)
                                        <h4 class="text-lg font-bold text-gray-900 mb-3">
                                            {!! $paymentInstruction->name !!}
                                        </h4>
                                    @endif

                                    @if ($paymentInstruction->description)
                                        <p class="text-gray-700 mb-4 leading-relaxed text-sm">
                                            {!! $paymentInstruction->description !!}
                                        </p>
                                    @endif

                                    <div class="bg-gray-50 p-4 rounded border border-gray-300">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @foreach ($paymentInstruction->fields as $key => $value)
                                                <div class="flex justify-between py-2 border-b border-gray-200">
                                                    @if (is_string($key))
                                                        <span class="font-semibold text-gray-800 text-sm">{{ $key }}:</span>
                                                        <span class="text-gray-700 text-right ml-4 font-medium text-sm">{!! $value !!}</span>
                                                    @else
                                                        <span class="text-gray-700 col-span-2 font-medium text-sm">{!! $value !!}</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                
                                @if ($paymentInstruction->qrcode)
                                    <div class="ml-6 flex-shrink-0">
                                        <div class="bg-white p-4 border-2 border-gray-300 rounded text-center">
                                            <img src="{{ $paymentInstruction->qrcode }}" class="w-32 h-32 mx-auto" alt="Código QR para pago">
                                            <p class="text-xs text-gray-600 mt-2 font-semibold">Escanea para pagar</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="mt-8 pt-4 border-t-2 border-gray-400">
        <div class="text-center mb-4">
            <p class="font-bold text-sm mb-2">Este documento es una representación impresa de la factura electrónica</p>
            <p class="text-xs text-gray-700">
                Factura válida según normativas vigentes de facturación electrónica
            </p>
        </div>
        
        <div class="bg-gray-50 border border-gray-300 p-4 text-xs text-gray-700">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p><strong>NIT Emisor:</strong> {{ $invoice->seller->tax_number ?? 'N/A' }}</p>
                    <p><strong>Razón Social:</strong> {{ $invoice->seller->company ?? $invoice->seller->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p><strong>Teléfono:</strong> {{ $invoice->seller->phone ?? 'N/A' }}</p>
                    <p><strong>Correo Electrónico:</strong> {{ $invoice->seller->email ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4 text-xs text-gray-600">
            <p>Para consultas o aclaraciones sobre esta factura, contacte al emisor</p>
        </div>
    </div>

</div>
