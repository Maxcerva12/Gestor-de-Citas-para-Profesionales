<div class="bg-white min-h-screen">
    <!-- Professional Header -->
    <div class="border-b-2 mb-6" style="border-color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}">
        <div class="flex justify-between items-start py-6">
            <div class="flex-1">
                <h1 class="text-4xl font-bold mb-2" style="color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}">
                    FACTURA DE VENTA
                </h1>
                <div class="text-sm text-gray-600 mb-4">
                    Estado: <span class="font-semibold px-2 py-1 rounded text-white text-xs" style="background-color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}">
                        {{ $invoice->getStateLabel() }}
                    </span>
                </div>
            </div>
            @if ($invoice->logo)
                <div class="text-right">
                    <img src="{{ $invoice->logo }}" alt="Logo Empresa" class="h-24 w-auto">
                </div>
            @endif
        </div>
    </div>

    <!-- Invoice Information Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Invoice Details -->
        <div class="bg-gray-50 p-6 rounded">
            <h2 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">INFORMACIÓN DE FACTURA</h2>
            <div class="space-y-3">
                <div class="grid grid-cols-3 gap-2">
                    <span class="font-semibold text-gray-700">No. Factura:</span>
                    <span class="col-span-2 font-bold" style="color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}">
                        {{ $invoice->serial_number }}
                    </span>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <span class="font-semibold text-gray-700">Fecha Emisión:</span>
                    <span class="col-span-2 text-gray-800">
                        {{ $invoice->created_at?->format(config('invoices.date_format')) }}
                    </span>
                </div>
                @if ($invoice->due_at)
                    <div class="grid grid-cols-3 gap-2">
                        <span class="font-semibold text-gray-700">Vencimiento:</span>
                        <span class="col-span-2 text-gray-800">
                            {{ $invoice->due_at->format(config('invoices.date_format')) }}
                        </span>
                    </div>
                @endif
                @if ($invoice->paid_at)
                    <div class="grid grid-cols-3 gap-2">
                        <span class="font-semibold text-gray-700">Fecha Pago:</span>
                        <span class="col-span-2 text-green-600 font-semibold">
                            {{ $invoice->paid_at->format(config('invoices.date_format')) }}
                        </span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Additional Fields -->
        @if ($invoice->fields && count($invoice->fields) > 0)
            <div class="bg-gray-50 p-6 rounded">
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

    <!-- Business Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Seller Information -->
        <div class="border border-gray-300 rounded p-6">
            <h3 class="text-lg font-bold mb-4 text-white px-4 py-2 rounded-t" style="background-color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}; margin: -24px -24px 16px -24px;">
                📋 DATOS DEL EMISOR
            </h3>
            <div class="text-sm text-gray-700 leading-relaxed">
                @include('invoices::default.includes.party', ['party' => $invoice->seller])
            </div>
        </div>

        <!-- Buyer Information -->
        <div class="border border-gray-300 rounded p-6">
            <h3 class="text-lg font-bold mb-4 text-white px-4 py-2 rounded-t" style="background-color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}; margin: -24px -24px 16px -24px;">
                👤 DATOS DEL CLIENTE
            </h3>
            <div class="text-sm text-gray-700 leading-relaxed">
                @include('invoices::default.includes.party', ['party' => $invoice->buyer])
            </div>
        </div>
    </div>

    <!-- Shipping Address (if exists) -->
    @if ($invoice->buyer->shipping_address)
        <div class="border border-gray-300 rounded p-6 mb-8">
            <h3 class="text-lg font-bold mb-4 text-white px-4 py-2 rounded-t" style="background-color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}; margin: -24px -24px 16px -24px;">
                🚚 DIRECCIÓN DE ENVÍO
            </h3>
            <div class="text-sm text-gray-700 leading-relaxed">
                @include('invoices::default.includes.address', ['address' => $invoice->buyer->shipping_address])
            </div>
        </div>
    @endif

    @php
        $hasTaxes = $invoice->tax_label || $invoice->totalTaxAmount()->isPositive();
    @endphp

    <!-- Professional Items Table -->
    <div class="mb-8">
        <div class="border border-gray-300 rounded overflow-hidden">
            <!-- Table Header -->
            <div class="text-white px-6 py-4" style="background-color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}">
                <h2 class="text-xl font-bold">📋 DETALLE DE PRODUCTOS Y SERVICIOS</h2>
            </div>
            
            <!-- Table Content -->
            <div class="bg-white">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b-2 border-gray-300">
                        <tr>
                            <th class="px-6 py-4 text-left font-bold text-gray-800 text-sm uppercase tracking-wide">
                                Descripción
                            </th>
                            <th class="px-4 py-4 text-center font-bold text-gray-800 text-sm uppercase tracking-wide">
                                Cant.
                            </th>
                            <th class="px-4 py-4 text-right font-bold text-gray-800 text-sm uppercase tracking-wide">
                                Precio Unit.
                            </th>
                            @if ($hasTaxes)
                                <th class="px-4 py-4 text-right font-bold text-gray-800 text-sm uppercase tracking-wide">
                                    Impuesto
                                </th>
                            @endif
                            <th class="px-6 py-4 text-right font-bold text-gray-800 text-sm uppercase tracking-wide">
                                Total
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

            <!-- Summary Section -->
            <div class="bg-gray-50 border-t-2 border-gray-300">
                <div class="px-6 py-6">
                    <div class="max-w-md ml-auto space-y-3">
                        <!-- Subtotal -->
                        <div class="flex justify-between py-2 border-b border-gray-300">
                            <span class="font-semibold text-gray-700 text-lg">Subtotal:</span>
                            <span class="font-bold text-gray-900 text-lg">{{ $invoice->formatMoney($invoice->subTotalAmount()) }}</span>
                        </div>

                        <!-- Discounts -->
                        @if ($invoice->discounts)
                            @foreach ($invoice->discounts as $discount)
                                <div class="flex justify-between py-2">
                                    <span class="text-gray-700">
                                        {{ $discount->name ?? 'Descuento' }}
                                        @if ($discount->percent_off)
                                            <span class="text-sm">({{ $discount->formatPercentage($discount->percent_off) }})</span>
                                        @endif
                                    </span>
                                    <span class="font-semibold text-red-600">
                                        -{{ $invoice->formatMoney($discount->computeDiscountAmountOn($invoice->subTotalAmount())) }}
                                    </span>
                                </div>
                            @endforeach
                            <div class="flex justify-between py-2 border-b border-gray-300">
                                <span class="font-semibold text-gray-700">Subtotal con Descuento:</span>
                                <span class="font-bold text-gray-900">{{ $invoice->formatMoney($invoice->subTotalDiscountedAmount()) }}</span>
                            </div>
                        @endif

                        <!-- Taxes -->
                        @if ($hasTaxes)
                            <div class="flex justify-between py-2 border-b border-gray-300">
                                <span class="font-semibold text-gray-700">
                                    IVA Colombia ({{ 
                                        number_format(
                                            collect($invoice->items)
                                                ->pluck('tax_percentage')
                                                ->filter()
                                                ->unique()
                                                ->implode(', ') ?: 19, 1
                                        ) 
                                    }}%):
                                </span>
                                <span class="font-bold text-gray-900">{{ $invoice->formatMoney($invoice->totalTaxAmount()) }}</span>
                            </div>
                        @endif

                        <!-- Total -->
                        <div class="flex justify-between py-4 border-2 rounded px-4" style="border-color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}; background-color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}10">
                            <span class="text-2xl font-bold" style="color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}">TOTAL A PAGAR:</span>
                            <span class="text-2xl font-bold" style="color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}">
                                {{ $invoice->formatMoney($invoice->totalAmount()) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Description -->
    @if ($invoice->description)
        <div class="mb-8">
            <div class="border border-gray-300 rounded overflow-hidden">
                <div class="text-white px-6 py-4" style="background-color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}">
                    <h3 class="text-lg font-bold">📄 OBSERVACIONES Y NOTAS ADICIONALES</h3>
                </div>
                <div class="p-6 bg-white">
                    <div class="text-gray-700 leading-relaxed whitespace-pre-line">
                        {!! $invoice->description !!}
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Payment Instructions -->
    @if ($invoice->paymentInstructions)
        <div class="mb-8">
            <div class="border border-gray-300 rounded overflow-hidden">
                <div class="text-white px-6 py-4" style="background-color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}">
                    <h3 class="text-lg font-bold">💳 INSTRUCCIONES DE PAGO</h3>
                </div>
                <div class="bg-white">
                    @foreach ($invoice->paymentInstructions as $paymentInstruction)
                        <div class="p-6 {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    @if ($paymentInstruction->name)
                                        <h4 class="text-xl font-bold text-gray-900 mb-3">
                                            {!! $paymentInstruction->name !!}
                                        </h4>
                                    @endif

                                    @if ($paymentInstruction->description)
                                        <p class="text-gray-700 mb-4 leading-relaxed">
                                            {!! $paymentInstruction->description !!}
                                        </p>
                                    @endif

                                    <div class="bg-gray-50 p-4 rounded border">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @foreach ($paymentInstruction->fields as $key => $value)
                                                <div class="flex justify-between py-2 border-b border-gray-200">
                                                    @if (is_string($key))
                                                        <span class="font-semibold text-gray-800">{{ $key }}:</span>
                                                        <span class="text-gray-700 text-right ml-4 font-medium">{!! $value !!}</span>
                                                    @else
                                                        <span class="text-gray-700 col-span-2 font-medium">{!! $value !!}</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                
                                @if ($paymentInstruction->qrcode)
                                    <div class="ml-6 flex-shrink-0">
                                        <div class="bg-white p-4 border-2 border-gray-300 rounded text-center">
                                            <img src="{{ $paymentInstruction->qrcode }}" class="w-32 h-32 mx-auto" alt="Código QR">
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

    <!-- Professional Footer -->
    <div class="text-center py-6 border-t-2 border-gray-300 text-gray-600">
        <p class="text-sm">
            Esta factura ha sido generada electrónicamente y es válida sin firma autógrafa.
        </p>
        <p class="text-xs mt-2">
            Para consultas sobre esta factura, contacte con nuestro departamento de facturación.
        </p>
    </div>

</div>
