<?php

namespace App\Models;

use App\Enums\InvoiceState;
use App\Enums\InvoiceType;
use Elegantly\Invoices\Models\Invoice as BaseInvoice;
use Elegantly\Invoices\Pdf\PdfInvoice;
use Elegantly\Invoices\Pdf\PdfInvoiceItem;
use Elegantly\Invoices\Support\Address;
use Elegantly\Invoices\Support\Buyer;
use Elegantly\Invoices\Support\Seller;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\File;

class Invoice extends BaseInvoice
{
    protected $attributes = [
        'type' => 'invoice',
        'state' => 'draft',
    ];

    protected function casts(): array
    {
        return [
            ...parent::casts(),
            'type' => InvoiceType::class,
            'state' => InvoiceState::class,
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'buyer_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getLogo(): ?string
    {
        // Usar configuración dinámica del logo
        return InvoiceSettings::getCompanyLogo();
    }

    public function toPdfInvoice(): PdfInvoice
    {
        // Limpiar caché para obtener la configuración más reciente
        InvoiceSettings::clearCache();

        // Obtener configuraciones específicamente
        $template = InvoiceSettings::get('invoice_template', 'colombia.layout');
        $color = InvoiceSettings::get('pdf_template_color', '#1e40af');
        $font = InvoiceSettings::get('pdf_font', 'Helvetica');

        $sellerInfo = InvoiceSettings::getCompanyInfo();
        $buyerInfo = $this->buyer_information ?? [];

        $templateData = [
            'color' => $color,
            'font' => $font,
            'watermark' => null, // Para facturas reales no mostrar watermark
        ];

        $templateData = [
            'color' => $color,
            'font' => $font,
            'watermark' => null, // Para facturas reales no mostrar watermark
        ];

        $pdfInvoice = new PdfInvoice(
            serial_number: $this->serial_number,
            state: $this->state->value,
            seller: new Seller(
                company: $sellerInfo['company'] ?? null,
                name: $sellerInfo['name'] ?? null,
                address: new Address(
                    street: $sellerInfo['address']['street'] ?? null,
                    city: $sellerInfo['address']['city'] ?? null,
                    postal_code: $sellerInfo['address']['postal_code'] ?? null,
                    state: $sellerInfo['address']['state'] ?? null,
                    country: $sellerInfo['address']['country'] ?? null,
                ),
                email: $sellerInfo['email'] ?? null,
                phone: $sellerInfo['phone'] ?? null,
                tax_number: $sellerInfo['tax_number'] ?? null,
                fields: $sellerInfo['fields'] ?? [],
            ),
            buyer: new Buyer(
                company: $buyerInfo['company'] ?? null,
                name: $buyerInfo['name'] ?? null,
                address: new Address(
                    street: $buyerInfo['address']['street'] ?? null,
                    city: $buyerInfo['address']['city'] ?? null,
                    postal_code: $buyerInfo['address']['postal_code'] ?? null,
                    state: $buyerInfo['address']['state'] ?? null,
                    country: $buyerInfo['address']['country'] ?? null,
                ),
                shipping_address: isset($buyerInfo['shipping_address']) ? new Address(
                    street: $buyerInfo['shipping_address']['street'] ?? null,
                    city: $buyerInfo['shipping_address']['city'] ?? null,
                    postal_code: $buyerInfo['shipping_address']['postal_code'] ?? null,
                    state: $buyerInfo['shipping_address']['state'] ?? null,
                    country: $buyerInfo['shipping_address']['country'] ?? null,
                ) : null,
                email: $buyerInfo['email'] ?? null,
                fields: $buyerInfo['fields'] ?? [],
            ),
            description: $this->description,
            created_at: $this->created_at,
            due_at: $this->due_at,
            paid_at: $this->paid_at ? \Carbon\Carbon::parse($this->paid_at) : null,
            tax_label: "IVA Colombia (" . InvoiceSettings::get('tax_rate', 19) . "%)",
            fields: [
                'Régimen Fiscal' => 'Común',
                'Medio de Pago' => 'Contado',
            ],
            items: $this->items->map(function ($item) {
                return new PdfInvoiceItem(
                    label: $item->label,
                    description: $item->description,
                    unit_price: $item->unit_price,
                    tax_percentage: $item->tax_percentage ?? InvoiceSettings::get('tax_rate', 19),
                    quantity: $item->quantity,
                );
            })->toArray(),
            logo: $this->getLogo(),
            template: $template,
        );

        // Establecer templateData después de la creación para evitar que sea sobrescrito
        $pdfInvoice->templateData = $templateData;

        return $pdfInvoice;
    }
    public function getTotalBeforeTaxAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->unit_price->getAmount() * $item->quantity;
        });
    }

    public function getTotalTaxAttribute()
    {
        return $this->items->sum(function ($item) {
            $subtotal = $item->unit_price->getAmount() * $item->quantity;
            return $subtotal * ($item->tax_percentage ?? 19) / 100;
        });
    }

    public function getTotalAttribute()
    {
        return $this->getTotalBeforeTaxAttribute() + $this->getTotalTaxAttribute();
    }
}
