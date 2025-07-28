@php
    $color = data_get($invoice->templateData, 'color', '#1e40af');
    $font = data_get($invoice->templateData, 'font', 'Helvetica');
@endphp

<!DOCTYPE html>
<html lang="es">

<head>
    <title>{{ $invoice->serial_number }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    @include('invoices::colombia.style')
</head>

<body>

    <!-- Encabezado con colores de Colombia -->
    <div class="fixed -left-12 -right-12 -top-12">
        <div class="h-1 w-full bg-yellow-400"></div>
        <div class="h-1 w-full bg-blue-600"></div>
        <div class="h-1 w-full bg-red-600"></div>
    </div>

    <!-- Pie de página -->
    <div class="fixed -bottom-14 -left-12 -right-12 mx-12 mb-12">
        <table class="w-full">
            <tbody>
                <tr class="text-xs text-gray-500">
                    <td class="">
                        {{ $invoice->serial_number }} • {{ $invoice->formatMoney($invoice->totalAmount()) }}
                    </td>
                    <td class="text-center">
                        <p class="text-xs">Esta factura es válida según la normativa colombiana - DIAN</p>
                    </td>
                    <td class="text-right">
                        <p class="dompdf-page p-2">Página </p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    @include('invoices::colombia.invoice')

</body>

</html>
