<div class="bg-white rounded-lg shadow-lg p-6 border" style="font-family: {{ $font }};">
    <!-- Header de la factura -->
    <div class="border-b-2 pb-4 mb-6" style="border-color: {{ $color }};">
        <div class="flex justify-between items-start">
            <div class="flex items-center space-x-4">
                @if($logo)
                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                    <img src="{{ Storage::url($logo) }}" alt="Logo" class="w-full h-full object-contain rounded-lg">
                </div>
                @else
                <div class="w-16 h-16 rounded-lg flex items-center justify-center text-white text-xl font-bold" style="background-color: {{ $color }};">
                    {{ substr($companyName, 0, 2) }}
                </div>
                @endif
                <div>
                    <h1 class="text-2xl font-bold" style="color: {{ $color }};">{{ $companyName ?: 'Mi Empresa' }}</h1>
                    <p class="text-gray-600">{{ $companyEmail ?: 'email@empresa.com' }}</p>
                    <p class="text-gray-600">{{ $companyPhone ?: '+57 123 456 7890' }}</p>
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-3xl font-bold" style="color: {{ $color }};">FACTURA</h2>
                <p class="text-gray-600">No. FACT-001</p>
                <p class="text-gray-600">{{ now()->format('d/m/Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Información del cliente (ejemplo) -->
    <div class="grid grid-cols-2 gap-6 mb-6">
        <div>
            <h3 class="font-semibold mb-2" style="color: {{ $color }};">FACTURAR A:</h3>
            <div class="bg-gray-50 p-3 rounded">
                <p class="font-medium">Cliente de Ejemplo</p>
                <p class="text-sm text-gray-600">cliente@ejemplo.com</p>
                <p class="text-sm text-gray-600">Calle Ejemplo 123</p>
                <p class="text-sm text-gray-600">Bogotá, Colombia</p>
            </div>
        </div>
        <div>
            <h3 class="font-semibold mb-2" style="color: {{ $color }};">DETALLES DE PAGO:</h3>
            <div class="bg-gray-50 p-3 rounded">
                <p class="text-sm"><span class="font-medium">Fecha de Vencimiento:</span> {{ now()->addDays(30)->format('d/m/Y') }}</p>
                <p class="text-sm"><span class="font-medium">Método de Pago:</span> Transferencia</p>
                <p class="text-sm"><span class="font-medium">Estado:</span> <span class="text-yellow-600">Pendiente</span></p>
            </div>
        </div>
    </div>

    <!-- Tabla de productos/servicios (ejemplo) -->
    <div class="mb-6">
        <table class="w-full border-collapse">
            <thead>
                <tr class="text-white" style="background-color: {{ $color }};">
                    <th class="border p-2 text-left">Descripción</th>
                    <th class="border p-2 text-center">Cantidad</th>
                    <th class="border p-2 text-right">Precio Unit.</th>
                    <th class="border p-2 text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b">
                    <td class="border p-2">
                        <div class="font-medium">Servicio de Consultoría</div>
                        <div class="text-sm text-gray-600">Consultoría profesional especializada</div>
                    </td>
                    <td class="border p-2 text-center">2</td>
                    <td class="border p-2 text-right">$150.000</td>
                    <td class="border p-2 text-right font-medium">$300.000</td>
                </tr>
                <tr class="border-b">
                    <td class="border p-2">
                        <div class="font-medium">Desarrollo de Software</div>
                        <div class="text-sm text-gray-600">Desarrollo de aplicación personalizada</div>
                    </td>
                    <td class="border p-2 text-center">1</td>
                    <td class="border p-2 text-right">$500.000</td>
                    <td class="border p-2 text-right font-medium">$500.000</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Totales -->
    <div class="flex justify-end">
        <div class="w-1/3">
            <div class="bg-gray-50 p-4 rounded">
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

    <!-- Footer legal -->
    <div class="mt-8 pt-4 border-t text-xs text-gray-500 text-center">
        <p>Esta es una factura legal válida en Colombia según la normativa DIAN</p>
        <p>Régimen Común - Somos responsables del IVA</p>
    </div>

    <!-- Nota informativa -->
    <div class="mt-4 p-3 bg-blue-50 rounded border-l-4" style="border-color: {{ $color }};">
        <p class="text-sm text-gray-700">
            <span class="font-medium">📋 Vista Previa:</span> 
            Esta es una vista previa de cómo se verá su factura con la configuración actual. 
            Los cambios se aplicarán automáticamente cuando guarde la configuración.
        </p>
    </div>
</div>
