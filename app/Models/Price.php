<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Price extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',    // <-- NUEVO
        'stripe_price_id',
        'name',
        'amount',
        'currency',
        'description',
        'duration',
        'is_active'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    // NUEVO: Relación con profesional
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}