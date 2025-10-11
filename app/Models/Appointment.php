<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_id',
        'schedule_id',
        'service_id',
        'start_time',
        'end_time',
        'status',
        'notes',
        'service_price',
        'payment_method',
        'payment_status',
        'google_event_id'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'status' => 'string',
        'service_price' => 'decimal:2',
        'payment_method' => 'string',
        'payment_status' => 'string'
    ];

    protected $attributes = [
        'payment_status' => 'pending',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Boot method para establecer el precio del servicio automáticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Si se selecciona un servicio y no se especifica precio, usar el precio del servicio
            if ($model->service_id && !$model->service_price && $model->service) {
                $model->service_price = $model->service->price;
            }
        });

        static::updating(function ($model) {
            // Si cambia el servicio, actualizar el precio automáticamente
            if ($model->isDirty('service_id') && $model->service_id && !$model->isDirty('service_price')) {
                $model->service_price = $model->service->price;
            }
        });
    }

    /**
     * Formatear el precio del servicio como moneda colombiana
     */
    public function getFormattedServicePriceAttribute(): string
    {
        return $this->service_price ? '$' . number_format((float) $this->service_price, 0, ',', '.') : 'N/A';
    }

    /**
     * Obtener el nombre del método de pago formateado
     */
    public function getFormattedPaymentMethodAttribute(): string
    {
        return match ($this->payment_method) {
            'efectivo' => 'Efectivo',
            'transferencia' => 'Transferencia',
            'tarjeta_debito' => 'Tarjeta de Débito',
            default => 'No especificado',
        };
    }

    /**
     * Obtener el estado del pago formateado
     */
    public function getFormattedPaymentStatusAttribute(): string
    {
        return match ($this->payment_status) {
            'pending' => 'Pendiente',
            'paid' => 'Pagado',
            'failed' => 'Fallido',
            'cancelled' => 'Cancelado',
            default => 'Desconocido',
        };
    }
}
