<?php

use Illuminate\Support\Facades\Route;
use App\Models\Invoice;

Route::get('/test-pdf-download', function () {
    try {
        $invoice = Invoice::first();
        
        if (!$invoice) {
            return response()->json(['error' => 'No hay facturas para probar']);
        }
        
        // Limpiar caché
        \App\Models\InvoiceSettings::clearCache();
        
        // Generar PDF
        $pdfInvoice = $invoice->toPdfInvoice();
        
        // Debug: mostrar los valores que se están usando
        $debug = [
            'templateData' => $pdfInvoice->templateData,
            'template' => $pdfInvoice->template,
            'color_from_db' => \App\Models\InvoiceSettings::get('pdf_template_color'),
            'font_from_db' => \App\Models\InvoiceSettings::get('pdf_font'),
        ];
        
        // Si hay parámetro debug, mostrar info
        if (request('debug')) {
            return response()->json($debug);
        }
        
        // Generar el PDF
        $pdfOutput = $pdfInvoice->getPdfOutput();
        
        return response($pdfOutput, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="test-invoice.pdf"',
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);
    }
});
