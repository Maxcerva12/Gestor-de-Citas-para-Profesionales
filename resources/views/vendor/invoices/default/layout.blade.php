@php
    $color = data_get($invoice->templateData, 'color', '#1e40af');
    $font = data_get($invoice->templateData, 'font', 'Helvetica');
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ $invoice->serial_number }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        * {
            font-family: {{ $font }}, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
        }
    </style>

    @include('invoices::default.style')
</head>

<body>

    <div class="fixed -left-12 -right-12 -top-12">
        <div class="h-2 w-full" style="background-color: {{ $color }}"></div>
    </div>

    <div class="fixed -bottom-14 -left-12 -right-12 mx-12 mb-12">
        <table class="w-full">
            <tbody>
                <tr class="text-xs text-gray-500">
                    <td class="">
                        {{ $invoice->serial_number }} • {{ $invoice->formatMoney($invoice->totalAmount()) }}
                    </td>
                    <td class="text-right">
                        <p class="p-2">Página <span class="dompdf-page"></span></p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    @include('invoices::default.invoice')

</body>

</html>
