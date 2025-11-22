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
        'discount_enabled' => false,
        'discount_percentage' => 0,
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

            // Guardar el estado actual del descuento en el momento de creación
            // Siempre obtener los valores actuales de configuración
            $discountEnabled = InvoiceSettings::get('discount_enabled', 'false');
            $model->discount_enabled = ($discountEnabled === 'true' || $discountEnabled === true || $discountEnabled === 1);

            if ($model->discount_enabled) {
                $model->discount_percentage = (float) InvoiceSettings::get('discount_percentage', 0);
            } else {
                $model->discount_percentage = 0;
            }
        });

        static::updating(function ($model) {
            // Forzar COP como moneda
            $model->currency = 'COP';
        });
    }

    /**
     * Override para manejar mejor los items y calcular totales denormalizados
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

            // Calcular totales denormalizados
            $subtotalAmount = 0;

            foreach ($this->items as $item) {
                // Manejar unit_price como objeto Money
                if (is_object($item->unit_price) && method_exists($item->unit_price, 'multipliedBy')) {
                    // Usar los métodos del objeto Money para hacer los cálculos
                    $itemSubtotal = $item->unit_price->multipliedBy($item->quantity);
                    $subtotalAmount += $itemSubtotal->getAmount()->toFloat();
                } else {
                    // Fallback para valores numéricos simples
                    $unitAmount = (float) $item->unit_price;
                    $itemSubtotal = $unitAmount * $item->quantity;
                    $subtotalAmount += $itemSubtotal;
                }
            }

            // Aplicar descuento al subtotal si está habilitado
            $discountAmount = 0;
            if ($this->discount_enabled && $this->discount_percentage > 0) {
                $discountAmount = $subtotalAmount * ($this->discount_percentage / 100);
                $subtotalAmount = $subtotalAmount - $discountAmount;
            }

            // Calcular el IVA sobre el subtotal después del descuento
            $taxAmount = $subtotalAmount * ($defaultTaxRate / 100);

            // Actualizar campos denormalizados sin disparar eventos
            $this->updateQuietly([
                'subtotal_amount' => (int) round($subtotalAmount),
                'tax_amount' => (int) round($taxAmount),
                'total_amount' => (int) round($subtotalAmount + $taxAmount),
                'currency' => 'COP'
            ]);

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
            'discount_enabled' => 'boolean',
            'discount_percentage' => 'decimal:2',
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

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
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

        // Si hay un cliente asociado, combinar su información con los campos personalizados
        if ($client) {
            $fullName = trim($client->name . ' ' . ($client->apellido ?? ''));

            // Obtener campos personalizados existentes que el usuario guardó
            $customFields = $buyerInfo['fields'] ?? [];

            // Preparar campos automáticos del cliente (solo los que tienen valor)
            $clientFields = array_filter([
                'Tipo de Documento' => $client->tipo_documento ?? 'Cédula de Ciudadanía',
                'Número de Documento' => $client->numero_documento,
                'Género' => $client->genero,
                'Fecha de Nacimiento' => $client->fecha_nacimiento ? \Carbon\Carbon::parse($client->fecha_nacimiento)->format('d/m/Y') : null,
                'Tipo de Sangre' => $client->tipo_sangre,
                'Aseguradora' => $client->aseguradora,
            ], function ($value) {
                return $value !== null && $value !== '';
            });

            // Combinar campos: los personalizados tienen prioridad sobre los automáticos
            $allFields = array_merge($clientFields, $customFields);

            $buyerInfo = [
                'company' => $buyerInfo['company'] ?? null,
                'name' => $buyerInfo['name'] ?? $fullName,
                'email' => $buyerInfo['email'] ?? $client->email,
                'phone' => $buyerInfo['phone'] ?? $client->phone,
                'address' => array_merge([
                    'street' => $client->address,
                    'city' => $client->city,
                    'postal_code' => null,
                    'state' => null,
                    'country' => $client->country,
                ], $buyerInfo['address'] ?? []),
                'fields' => $allFields,
            ];
        }

        $templateData = [
            'color' => $color,
            'font' => $font,
            'watermark' => null, // Para facturas reales no mostrar watermark
        ];

        // Traducir el estado al español
        $stateLabel = match($this->state->value) {
            'draft' => 'Borrador',
            'sent' => 'Enviada',
            'paid' => 'Pagada',
            'overdue' => 'Vencida',
            'cancelled' => 'Cancelada',
            default => $this->state->value
        };

        $pdfInvoice = new PdfInvoice(
            serial_number: $this->serial_number,
            state: $stateLabel,
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
            paid_at: null, // No manejamos pagos en este sistema
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
