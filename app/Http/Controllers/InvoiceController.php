<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InvoiceController extends Controller
{
    use AuthorizesRequests;

    /**
     * Mostrar la factura como PDF en el navegador
     */
    public function viewPdf(Invoice $invoice)
    {
        try {
            // Limpiar caché antes de generar el PDF para asegurar configuración actualizada
            \App\Models\InvoiceSettings::clearCache();

            $pdfInvoice = $invoice->toPdfInvoice();

            return response($pdfInvoice->getPdfOutput(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $invoice->serial_number . '.pdf"',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al generar PDF para ver: ' . $e->getMessage(), [
                'invoice_id' => $invoice->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Error al generar el PDF',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Descargar la factura como PDF
     */
    public function downloadPdf(Invoice $invoice)
    {
        try {
            // Limpiar caché antes de generar el PDF para asegurar configuración actualizada
            \App\Models\InvoiceSettings::clearCache();

            $pdfInvoice = $invoice->toPdfInvoice();

            return response($pdfInvoice->getPdfOutput(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $invoice->serial_number . '.pdf"',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al generar PDF para descargar: ' . $e->getMessage(), [
                'invoice_id' => $invoice->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Error al generar el PDF',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vista previa de la plantilla (para personalización)
     */
    public function templatePreview(Request $request)
    {
        // Esta será para el preview de personalización de plantillas
        $sampleInvoice = $this->createSampleInvoice();

        $templateData = [
            'color' => $request->get('color', '#1e40af'),
            'font' => $request->get('font', 'Helvetica'),
            'logo' => $request->get('logo'),
        ];

        $pdfInvoice = $sampleInvoice->toPdfInvoice();
        $pdfInvoice->templateData = array_merge($pdfInvoice->templateData, $templateData);

        if ($request->get('format') === 'html') {
            return $pdfInvoice->view();
        }

        return response($pdfInvoice->getPdfOutput(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="preview.pdf"',
        ]);
    }

    /**
     * Crear una factura de muestra para preview
     */
    private function createSampleInvoice(): Invoice
    {
        // Crear una factura temporal para preview
        $invoice = new Invoice([
            'serial_number' => 'FAC250001',
            'type' => 'invoice',
            'state' => 'draft',
            'description' => 'Factura de muestra para preview',
            'due_at' => now()->addDays(30),
            'seller_information' => config('invoices.default_seller'),
            'buyer_information' => [
                'company' => 'Cliente de Ejemplo S.A.S.',
                'name' => 'Juan Pérez',
                'email' => 'juan.perez@ejemplo.com',
                'phone' => '+57 (1) 234-5678',
                'address' => [
                    'street' => 'Calle 123 #45-67',
                    'city' => 'Bogotá',
                    'postal_code' => '110111',
                    'state' => 'Cundinamarca',
                    'country' => 'Colombia',
                ],
                'fields' => [
                    'NIT' => '900.123.456-7',
                    'Régimen' => 'Común',
                ],
            ],
        ]);

        // Simular items
        $invoice->setRelation('items', collect([
            (object) [
                'label' => 'Consultoría en Software',
                'description' => 'Desarrollo de aplicación web personalizada',
                'unit_price' => \Brick\Money\Money::of(500000, 'COP'),
                'quantity' => 2,
                'tax_percentage' => 19,
            ],
            (object) [
                'label' => 'Hosting y Dominio',
                'description' => 'Servicio anual de hosting y dominio',
                'unit_price' => \Brick\Money\Money::of(200000, 'COP'),
                'quantity' => 1,
                'tax_percentage' => 19,
            ],
        ]));

        return $invoice;
    }
}
