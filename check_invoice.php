<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Verificar última factura
$invoice = App\Models\Invoice::latest()->first();

if ($invoice) {
    echo "=== ÚLTIMA FACTURA ===" . PHP_EOL;
    echo "ID: " . $invoice->id . PHP_EOL;
    echo "Serial: " . $invoice->serial_number . PHP_EOL;
    echo "Discount Enabled: " . ($invoice->discount_enabled ? 'SI' : 'NO') . PHP_EOL;
    echo "Discount %: " . $invoice->discount_percentage . '%' . PHP_EOL;
    $subtotal = is_object($invoice->subtotal_amount) ? $invoice->subtotal_amount->getAmount()->toFloat() : (float) $invoice->subtotal_amount;
    $tax = is_object($invoice->tax_amount) ? $invoice->tax_amount->getAmount()->toFloat() : (float) $invoice->tax_amount;
    $total = is_object($invoice->total_amount) ? $invoice->total_amount->getAmount()->toFloat() : (float) $invoice->total_amount;

    echo "Subtotal: $" . number_format($subtotal, 0, ',', '.') . PHP_EOL;
    echo "IVA: $" . number_format($tax, 0, ',', '.') . PHP_EOL;
    echo "Total: $" . number_format($total, 0, ',', '.') . PHP_EOL;
    echo PHP_EOL;

    // Verificar configuración actual
    echo "=== CONFIGURACIÓN ACTUAL ===" . PHP_EOL;
    $discountEnabled = App\Models\InvoiceSettings::get('discount_enabled', 'false');
    $discountPercentage = App\Models\InvoiceSettings::get('discount_percentage', 0);
    echo "Discount Config Enabled: " . $discountEnabled . PHP_EOL;
    echo "Discount Config %: " . $discountPercentage . '%' . PHP_EOL;
} else {
    echo "No hay facturas en la base de datos" . PHP_EOL;
}
