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
        'currency' => 'COP',
    ];

    /**
     * Boot method para establecer valores por defecto
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Forzar COP como moneda
            $model->currency = 'COP';
        });

        static::updating(function ($model) {
            // Forzar COP como moneda
            $model->currency = 'COP';
        });
    }

    /**
     * Override para manejar mejor los items y evitar errores de índice
     */
    public function denormalize(): static
    {
        try {
            // Asegurar que todos los items tengan datos válidos antes de denormalizar
            $this->items()->whereNull('currency')->update(['currency' => 'COP']);

            // Obtener el valor actual del tax_rate sin caché
            $taxRateSetting = \App\Models\InvoiceSettings::where('key', 'tax_rate')->first();
            $defaultTaxRate = $taxRateSetting ? (float) $taxRateSetting->value : 19;

            $this->items()->whereNull('tax_percentage')->update([
                'tax_percentage' => $defaultTaxRate
            ]);

            // Simplemente retornar this sin llamar al método padre problemático
            return $this;
        } catch (\Exception $e) {
            // Log el error pero no interrumpir la operación
            \Log::warning('Invoice denormalize failed: ' . $e->getMessage(), [
                'invoice_id' => $this->id,
                'items_count' => $this->items()->count()
            ]);

            return $this;
        }
    }

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

        // Obtener configuraciones específicamente - SIEMPRE obtener valor fresh
        $taxRate = InvoiceSettings::where('key', 'tax_rate')->first();
        $currentTaxRate = $taxRate ? (float) $taxRate->value : 19;

        $template = InvoiceSettings::get('invoice_template', 'colombia.layout');
        $color = InvoiceSettings::get('pdf_template_color', '#1e40af');
        $font = InvoiceSettings::get('pdf_font', 'Helvetica');

        $sellerInfo = InvoiceSettings::getCompanyInfo();

        // Obtener información completa del cliente
        $client = $this->client;
        $buyerInfo = $this->buyer_information ?? [];

        // Si hay un cliente asociado, usar su información
        if ($client) {
            $fullName = trim($client->name . ' ' . ($client->apellido ?? ''));
            $buyerInfo = [
                'company' => null,
                'name' => $fullName,
                'email' => $client->email,
                'phone' => $client->phone,
                'address' => [
                    'street' => $client->address,
                    'city' => $client->city,
                    'postal_code' => null,
                    'state' => null,
                    'country' => $client->country,
                ],
                'fields' => [
                    'Tipo de Documento' => $client->tipo_documento ?? 'Cédula de Ciudadanía',
                    'Número de Documento' => $client->numero_documento,
                    'Género' => $client->genero,
                    'Fecha de Nacimiento' => $client->fecha_nacimiento ? \Carbon\Carbon::parse($client->fecha_nacimiento)->format('d/m/Y') : null,
                    'Tipo de Sangre' => $client->tipo_sangre,
                    'Aseguradora' => $client->aseguradora,
                ],
            ];
        }

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
                phone: $buyerInfo['phone'] ?? null,
                fields: array_filter($buyerInfo['fields'] ?? []),
            ),
            description: $this->description,
            created_at: $this->created_at,
            due_at: $this->due_at,
            paid_at: $this->paid_at ? \Carbon\Carbon::parse($this->paid_at) : null,
            tax_label: "IVA Colombia (" . $currentTaxRate . "%)",
            fields: [
                'Régimen Fiscal' => 'Común',
                'Medio de Pago' => 'Contado',
            ],
            items: $this->items->map(function ($item) use ($currentTaxRate) {
                return new PdfInvoiceItem(
                    label: $item->label,
                    description: $item->description,
                    unit_price: $item->unit_price,
                    tax_percentage: $item->tax_percentage ?? $currentTaxRate,
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
        // Obtener el valor actual del tax_rate sin caché
        $taxRateSetting = \App\Models\InvoiceSettings::where('key', 'tax_rate')->first();
        $currentTaxRate = $taxRateSetting ? (float) $taxRateSetting->value : 19;

        return $this->items->sum(function ($item) use ($currentTaxRate) {
            $subtotal = $item->unit_price->getAmount() * $item->quantity;
            $taxPercentage = $item->tax_percentage ?? $currentTaxRate;

            // Asegurar que se use como porcentaje (dividir por 100)
            return $subtotal * ($taxPercentage / 100);
        });
    }

    public function getTotalAttribute()
    {
        return $this->getTotalBeforeTaxAttribute() + $this->getTotalTaxAttribute();
    }
}
