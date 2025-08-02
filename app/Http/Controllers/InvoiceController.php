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
            'description' => 'Factura de servicios odontológicos - Muestra para preview',
            'due_at' => now()->addDays(30),
            'seller_information' => config('invoices.default_seller'),
            'buyer_information' => [
                'company' => null,
                'name' => 'María Pérez González',
                'email' => 'maria.perez@ejemplo.com',
                'phone' => '+57 (1) 234-5678',
                'address' => [
                    'street' => 'Calle 123 #45-67',
                    'city' => 'Bogotá',
                    'postal_code' => '110111',
                    'state' => 'Cundinamarca',
                    'country' => 'Colombia',
                ],
                'fields' => [
                    'Cédula' => '52.123.456',
                    'EPS' => 'SURA',
                    'Tipo de Documento' => 'Cédula de Ciudadanía',
                ],
            ],
        ]);

        // Crear items de muestra para servicios odontológicos
        $taxRate = \App\Models\InvoiceSettings::get('tax_rate', 19);

        $invoice->setRelation('items', collect([
            (object) [
                'label' => 'Limpieza Dental Profunda',
                'description' => 'Profilaxis dental completa con aplicación de flúor',
                'unit_price' => \Brick\Money\Money::of(120000, 'COP'),
                'quantity' => 1,
                'tax_percentage' => $taxRate,
            ],
            (object) [
                'label' => 'Obturación en Resina',
                'description' => 'Restauración dental con resina fotopolimerizable',
                'unit_price' => \Brick\Money\Money::of(180000, 'COP'),
                'quantity' => 2,
                'tax_percentage' => $taxRate,
            ],
            (object) [
                'label' => 'Consulta Odontológica',
                'description' => 'Consulta de control y valoración general',
                'unit_price' => \Brick\Money\Money::of(80000, 'COP'),
                'quantity' => 1,
                'tax_percentage' => $taxRate,
            ],
        ]));

        return $invoice;
    }
}
