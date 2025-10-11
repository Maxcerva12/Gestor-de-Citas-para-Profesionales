<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price',
        'duration', // en minutos
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $attributes = [
        'is_active' => true,
    ];

    /**
     * Boot method para establecer valores por defecto
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Si no se especifica un user_id, usar el usuario autenticado
            if (!$model->user_id && auth()->check()) {
                $model->user_id = auth()->id();
            }
        });
    }

    /**
     * Relación con el profesional que ofrece el servicio
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con las citas que usan este servicio
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Scope para servicios activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para servicios del usuario autenticado
     */
    public function scopeForUser($query, $userId = null)
    {
        $userId = $userId ?: auth()->id();
        return $query->where('user_id', $userId);
    }

    /**
     * Formatear el precio como moneda colombiana
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format((float) $this->price, 0, ',', '.');
    }

    /**
     * Formatear la duración en formato legible
     */
    public function getFormattedDurationAttribute(): string
    {
        $hours = floor($this->duration / 60);
        $minutes = $this->duration % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}min";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$minutes}min";
        }
    }

    /**
     * Obtener el nombre completo del servicio con precio
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->name} - {$this->formatted_price}";
    }
}