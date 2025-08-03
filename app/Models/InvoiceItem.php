<?php

namespace App\Models;

use Brick\Math\RoundingMode;
use Elegantly\Invoices\Models\InvoiceItem as BaseInvoiceItem;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends BaseInvoiceItem
{
    protected $fillable = [
        'invoice_id',
        'label',
        'description',
        'unit_price',
        'tax_percentage',
        'quantity',
        'order',
        'currency',
    ];

    protected function casts(): array
    {
        return [
            ...parent::casts(),
            'tax_percentage' => 'decimal:2',
            'quantity' => 'integer',
        ];
    }

    /**
     * Accessor para asegurar que tax_percentage siempre sea un porcentaje válido
     */
    public function getTaxPercentageAttribute($value)
    {
        if ($value === null) {
            // Obtener valor fresco sin caché
            $taxRateSetting = \App\Models\InvoiceSettings::where('key', 'tax_rate')->first();
            return $taxRateSetting ? (float) $taxRateSetting->value : 19;
        }

        // Si el valor es menor a 1, probablemente es decimal y debe convertirse
        if ($value < 1 && $value > 0) {
            return $value * 100;
        }

        return (float) $value;
    }

    /**
     * Mutator para asegurar que tax_percentage se guarde correctamente
     */
    public function setTaxPercentageAttribute($value)
    {
        if ($value === null) {
            // Obtener valor fresco sin caché
            $taxRateSetting = \App\Models\InvoiceSettings::where('key', 'tax_rate')->first();
            $defaultRate = $taxRateSetting ? (float) $taxRateSetting->value : 19;
            $this->attributes['tax_percentage'] = $defaultRate;
        } elseif ($value < 1 && $value > 0) {
            // Si es decimal (0.19), convertir a porcentaje (19)
            $this->attributes['tax_percentage'] = $value * 100;
        } else {
            $this->attributes['tax_percentage'] = (float) $value;
        }
    }

    /**
     * Boot method para establecer COP por defecto
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Forzar COP como moneda
            $model->currency = 'COP';

            // Asegurar que tax_percentage tenga un valor válido y sea un porcentaje, no decimal
            if ($model->tax_percentage === null) {
                // Obtener valor fresco sin caché
                $taxRateSetting = \App\Models\InvoiceSettings::where('key', 'tax_rate')->first();
                $model->tax_percentage = $taxRateSetting ? (float) $taxRateSetting->value : 19;
            } elseif ($model->tax_percentage < 1) {
                // Si es decimal (0.19), convertir a porcentaje (19)
                $model->tax_percentage = $model->tax_percentage * 100;
            }
        });

        static::updating(function ($model) {
            // Forzar COP como moneda
            $model->currency = 'COP';

            // Asegurar que tax_percentage tenga un valor válido y sea un porcentaje, no decimal
            if ($model->tax_percentage === null) {
                // Obtener valor fresco sin caché
                $taxRateSetting = \App\Models\InvoiceSettings::where('key', 'tax_rate')->first();
                $model->tax_percentage = $taxRateSetting ? (float) $taxRateSetting->value : 19;
            } elseif ($model->tax_percentage < 1) {
                // Si es decimal (0.19), convertir a porcentaje (19)
                $model->tax_percentage = $model->tax_percentage * 100;
            }
        });
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function getSubtotalAttribute()
    {
        return $this->unit_price->multipliedBy($this->quantity);
    }

    public function getTaxAmountAttribute()
    {
        $subtotal = $this->getSubtotalAttribute();
        return $subtotal->multipliedBy($this->tax_percentage ?? 19)->dividedBy(100, RoundingMode::HALF_UP);
    }

    public function getTotalAttribute()
    {
        return $this->getSubtotalAttribute()->plus($this->getTaxAmountAttribute());
    }
}
