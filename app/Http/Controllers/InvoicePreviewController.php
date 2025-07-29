<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InvoiceSettings;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Client;
use Elegantly\Invoices\Pdf\PdfInvoice;
use Elegantly\Invoices\Pdf\PdfInvoiceItem;
use Elegantly\Invoices\Support\Address;
use Elegantly\Invoices\Support\Buyer;
use Elegantly\Invoices\Support\Seller;
use Brick\Money\Money;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InvoicePreviewController extends Controller
{
    public function generatePreview(Request $request)
    {
        try {
            // Limpiar caché para obtener configuración más reciente
            InvoiceSettings::clearCache();

            // Crear una factura de ejemplo para la vista previa
            $sellerInfo = InvoiceSettings::getCompanyInfo();

            // Obtener configuración desde request o fallback a base de datos
            $template = $request->get('template', InvoiceSettings::get('invoice_template', 'colombia.layout'));
            $color = $request->get('color', InvoiceSettings::get('pdf_template_color', '#1e40af'));
            $font = $request->get('font', InvoiceSettings::get('pdf_font', 'Helvetica'));

            $pdfInvoice = new PdfInvoice(
                serial_number: 'PREVIEW-' . now()->format('YmdHis'),
                state: 'draft',
                seller: new Seller(
                    company: $sellerInfo['company'] ?? 'Mi Empresa',
                    name: $sellerInfo['name'] ?? 'Administrador',
                    address: new Address(
                        street: $sellerInfo['address']['street'] ?? 'Calle Ejemplo 123',
                        city: $sellerInfo['address']['city'] ?? 'Bogotá',
                        postal_code: $sellerInfo['address']['postal_code'] ?? '110111',
                        state: $sellerInfo['address']['state'] ?? 'Cundinamarca',
                        country: $sellerInfo['address']['country'] ?? 'Colombia',
                    ),
                    email: $sellerInfo['email'] ?? 'email@empresa.com',
                    phone: $sellerInfo['phone'] ?? '+57 123 456 7890',
                    tax_number: $sellerInfo['tax_number'] ?? '900123456-1',
                    fields: [
                        'Régimen Fiscal' => 'Común',
                        'Actividad Económica' => 'Servicios Profesionales',
                    ],
                ),
                buyer: new Buyer(
                    company: 'Cliente de Ejemplo S.A.S.',
                    name: 'Juan Pérez',
                    address: new Address(
                        street: 'Carrera 15 # 93-47',
                        city: 'Bogotá',
                        postal_code: '110111',
                        state: 'Cundinamarca',
                        country: 'Colombia',
                    ),
                    email: 'cliente@ejemplo.com',
                    fields: [
                        'NIT' => '800123456-7',
                        'Teléfono' => '+57 321 654 9870',
                    ],
                ),
                description: 'Factura de vista previa - Configuración actual del sistema',
                created_at: now(),
                due_at: now()->addDays(30),
                paid_at: null,
                tax_label: "IVA Colombia (" . InvoiceSettings::get('tax_rate', 19) . "%)",
                fields: [
                    'Régimen Fiscal' => 'Común',
                    'Medio de Pago' => 'Transferencia Bancaria',
                    'Observaciones' => 'Esta es una factura de vista previa generada automáticamente',
                ],
                items: [
                    new PdfInvoiceItem(
                        label: 'Servicio de Consultoría',
                        description: 'Consultoría profesional especializada en desarrollo de software',
                        unit_price: Money::of(150000, InvoiceSettings::get('currency', 'COP')),
                        tax_percentage: InvoiceSettings::get('tax_rate', 19),
                        quantity: 2,
                    ),
                    new PdfInvoiceItem(
                        label: 'Desarrollo de Software',
                        description: 'Desarrollo de aplicación web personalizada con tecnologías modernas',
                        unit_price: Money::of(500000, InvoiceSettings::get('currency', 'COP')),
                        tax_percentage: InvoiceSettings::get('tax_rate', 19),
                        quantity: 1,
                    ),
                    new PdfInvoiceItem(
                        label: 'Soporte Técnico',
                        description: 'Soporte técnico y mantenimiento por 3 meses',
                        unit_price: Money::of(100000, InvoiceSettings::get('currency', 'COP')),
                        tax_percentage: InvoiceSettings::get('tax_rate', 19),
                        quantity: 1,
                    ),
                ],
                logo: InvoiceSettings::getCompanyLogo(),
                template: $template,
            );

            // Establecer templateData después de la creación para evitar que sea sobrescrito
            $pdfInvoice->templateData = [
                'color' => $color,
                'font' => $font,
                'watermark' => 'VISTA PREVIA',
            ];

            // Generar el PDF usando el método correcto
            $pdfOutput = $pdfInvoice->getPdfOutput();

            // Devolver el PDF como respuesta
            return response($pdfOutput, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="factura-preview-' . now()->format('YmdHis') . '.pdf"',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al generar la vista previa',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
