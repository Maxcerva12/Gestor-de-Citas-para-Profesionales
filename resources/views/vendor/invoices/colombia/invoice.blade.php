<div class="colombia-invoice">
    <!-- Cabecera de la factura -->
    <table class="mb-8 w-full">
        <tbody>
            <tr>
                <td class="p-0 align-top">
                    <h1 class="mb-1 text-3xl font-bold" style="color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}">
                        FACTURA DE VENTA
                    </h1>
                    <p class="mb-2 text-sm font-semibold text-gray-600">
                        {{ $invoice->getStateLabel() }}
                    </p>

                    <table class="w-full border border-gray-300">
                        <tbody class="bg-gray-50">
                            <tr class="text-xs border-b">
                                <td class="whitespace-nowrap pr-2 py-1 px-2 font-semibold">
                                    <strong>Número de Factura:</strong>
                                </td>
                                <td class="whitespace-nowrap py-1 px-2" width="100%">
                                    <strong>{{ $invoice->serial_number }}</strong>
                                </td>
                            </tr>
                            <tr class="text-xs border-b">
                                <td class="whitespace-nowrap pr-2 py-1 px-2 font-semibold">
                                    Fecha de Emisión:
                                </td>
                                <td class="py-1 px-2" width="100%">
                                    {{ $invoice->created_at?->format('d/m/Y') }}
                                </td>
                            </tr>
                            @if ($invoice->due_at)
                                <tr class="text-xs border-b">
                                    <td class="whitespace-nowrap pr-2 py-1 px-2 font-semibold">
                                        Fecha de Vencimiento:
                                    </td>
                                    <td class="py-1 px-2" width="100%">
                                        {{ $invoice->due_at->format('d/m/Y') }}
                                    </td>
                                </tr>
                            @endif
                            @if ($invoice->paid_at)
                                <tr class="text-xs border-b">
                                    <td class="whitespace-nowrap pr-2 py-1 px-2 font-semibold">
                                        Fecha de Pago:
                                    </td>
                                    <td class="py-1 px-2" width="100%">
                                        {{ $invoice->paid_at->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            @endif
                            <tr class="text-xs">
                                <td class="whitespace-nowrap pr-2 py-1 px-2 font-semibold">
                                    Moneda:
                                </td>
                                <td class="py-1 px-2" width="100%">
                                    Peso Colombiano (COP)
                                </td>
                            </tr>

                            @foreach ($invoice->fields as $key => $value)
                                <tr class="text-xs border-b">
                                    <td class="whitespace-nowrap pr-2 py-1 px-2 font-semibold">
                                        {{ $key }}:
                                    </td>
                                    <td class="py-1 px-2" width="100%">
                                        {{ $value }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
                @if ($invoice->logo)
                    <td class="p-0 align-top" width="25%">
                        <div class="text-center">
                            <img src="{{ $invoice->logo }}" alt="Logo de la empresa" height="120" class="mx-auto mb-2">
                            <p class="text-xs font-semibold" style="color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}">
                                República de Colombia
                            </p>
                        </div>
                    </td>
                @endif
            </tr>
        </tbody>
    </table>

    <!-- Información del vendedor y comprador -->
    <table class="mb-6 w-full">
        <tbody>
            <tr>
                <td class="align-top border border-gray-300 p-3" width="50%">
                    <p class="mb-2 text-sm font-bold text-white px-2 py-1" style="background-color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}">
                        DATOS DEL EMISOR
                    </p>

                    @include('invoices::colombia.includes.party', [
                        'party' => $invoice->seller,
                        'isEmitter' => true,
                    ])
                </td>

                <td class="align-top border border-gray-300 p-3" width="50%">
                    <p class="mb-2 text-sm font-bold text-white px-2 py-1" style="background-color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}">
                        DATOS DEL ADQUIRIENTE
                    </p>

                    @include('invoices::colombia.includes.party', [
                        'party' => $invoice->buyer,
                        'isEmitter' => false,
                    ])
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Descripción de la factura -->
    @if ($invoice->description)
        <div class="mb-4 p-3 border border-gray-300 bg-gray-50">
            <p class="text-sm font-bold mb-1">Observaciones:</p>
            <p class="text-sm">{{ $invoice->description }}</p>
        </div>
    @endif

    <!-- Items de la factura -->
    <div class="mb-6">
        <table class="w-full border-collapse border border-gray-400">
            <thead>
                <tr class="text-white text-xs" style="background-color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}">
                    <th class="border border-gray-400 p-2 text-left">Descripción</th>
                    <th class="border border-gray-400 p-2 text-center" width="8%">Cant.</th>
                    <th class="border border-gray-400 p-2 text-right" width="12%">V. Unitario</th>
                    <th class="border border-gray-400 p-2 text-center" width="8%">IVA %</th>
                    <th class="border border-gray-400 p-2 text-right" width="12%">V. IVA</th>
                    <th class="border border-gray-400 p-2 text-right" width="12%">Subtotal</th>
                    <th class="border border-gray-400 p-2 text-right" width="12%">Total</th>
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
                    <tr class="text-xs {{ $loop->even ? 'bg-gray-50' : '' }}">
                        <td class="border border-gray-300 p-2">
                            <strong>{{ $item->label }}</strong>
                            @if ($item->description)
                                <br><span class="text-gray-600">{{ $item->description }}</span>
                            @endif
                        </td>
                        <td class="border border-gray-300 p-2 text-center">{{ $item->quantity }}</td>
                        <td class="border border-gray-300 p-2 text-right">{{ $invoice->formatMoney($item->unit_price) }}</td>
                        <td class="border border-gray-300 p-2 text-center">{{ number_format($taxRate, 1) }}%</td>
                        <td class="border border-gray-300 p-2 text-right">{{ $invoice->formatMoney($taxAmount) }}</td>
                        <td class="border border-gray-300 p-2 text-right">{{ $invoice->formatMoney($subtotal) }}</td>
                        <td class="border border-gray-300 p-2 text-right font-semibold">{{ $invoice->formatMoney($total) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Totales -->
    <div class="mb-6">
        @php
            // Calcular subtotal, impuestos y total manualmente
            $subtotal = collect($invoice->items)->reduce(function ($carry, $item) {
                return $carry ? $carry->plus($item->unit_price->multipliedBy($item->quantity)) : $item->unit_price->multipliedBy($item->quantity);
            }, null) ?: \Brick\Money\Money::of(0, 'COP');
            
            $totalTax = collect($invoice->items)->reduce(function ($carry, $item) {
                $itemSubtotal = $item->unit_price->multipliedBy($item->quantity);
                $itemTax = $itemSubtotal->multipliedBy($item->tax_percentage ?? 19)->dividedBy(100, \Brick\Math\RoundingMode::HALF_UP);
                return $carry ? $carry->plus($itemTax) : $itemTax;
            }, null) ?: \Brick\Money\Money::of(0, 'COP');
            
            $total = $subtotal->plus($totalTax);
        @endphp
    
        <table class="ml-auto" width="40%">
            <tbody>
                <tr class="text-sm border-b">
                    <td class="py-1 pr-4 text-right font-semibold">Subtotal:</td>
                    <td class="py-1 text-right">{{ $invoice->formatMoney($subtotal) }}</td>
                </tr>
                
                {{-- Descuentos comentados temporalmente --}}
                {{-- @if ($invoice->discountAmount()->isPositive())
                    <tr class="text-sm border-b text-red-600">
                        <td class="py-1 pr-4 text-right font-semibold">Descuentos:</td>
                        <td class="py-1 text-right">-{{ $invoice->formatMoney($invoice->discountAmount()) }}</td>
                    </tr>
                @endif --}}
                
                <tr class="text-sm border-b">
                    <td class="py-1 pr-4 text-right font-semibold">IVA (19%):</td>
                    <td class="py-1 text-right">{{ $invoice->formatMoney($totalTax) }}</td>
                </tr>
                
                <tr class="text-lg font-bold" style="color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}">
                    <td class="py-2 pr-4 text-right border-t-2 border-gray-400">TOTAL A PAGAR:</td>
                    <td class="py-2 text-right border-t-2 border-gray-400">{{ $invoice->formatMoney($total) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Instrucciones de pago -->
    @if ($invoice->paymentInstructions && count($invoice->paymentInstructions) > 0)
        <div class="mb-6">
            <h3 class="text-lg font-bold mb-3" style="color: {{ data_get($invoice->templateData, 'color', '#1e40af') }}">
                Información de Pago
            </h3>
            @foreach ($invoice->paymentInstructions as $instruction)
                <div class="border border-gray-300 p-3 mb-3">
                    <h4 class="font-semibold text-sm mb-2">{{ $instruction->name }}</h4>
                    @if ($instruction->description)
                        <p class="text-xs text-gray-600 mb-2">{{ $instruction->description }}</p>
                    @endif
                    
                    <div class="flex">
                        <div class="flex-1">
                            @if ($instruction->fields)
                                <table class="text-xs">
                                    @foreach ($instruction->fields as $key => $value)
                                        <tr>
                                            <td class="pr-3 font-semibold">{{ $key }}:</td>
                                            <td>{!! $value !!}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            @endif
                        </div>
                        
                        @if ($instruction->qrcode)
                            <div class="ml-4">
                                <img src="{{ $instruction->qrcode }}" alt="QR Code" width="80" height="80">
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Pie de factura colombiano -->
    <div class="mt-8 text-xs text-gray-600 border-t pt-4">
        <div class="text-center">
            <p class="font-semibold">FACTURA ELECTRÓNICA DE VENTA</p>
            <p>Esta factura es válida según el Decreto 1625 de 2016 y demás normas vigentes sobre facturación electrónica</p>
            <p class="mt-2">
                <strong>NIT:</strong> {{ $invoice->seller->tax_number ?? 'N/A' }} |
                <strong>Régimen:</strong> {{ data_get($invoice->seller->fields, 'Régimen', 'Común') }}
            </p>
        </div>
    </div>
</div>
