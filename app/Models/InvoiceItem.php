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
     * Boot method para establecer COP por defecto
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
