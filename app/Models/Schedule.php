<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'end_time',
        'is_available',
    ];

    protected $casts = [
        'date' => 'date',
        // Quitar los casts de hora para evitar conversiones automáticas
        // 'start_time' => 'datetime:H:i',
        // 'end_time' => 'datetime:H:i',
        'is_available' => 'boolean',
    ];

    /**
     * Relación con el profesional (User).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Verifica si el horario ha expirado (fecha/hora ya pasó)
     */
    public function isExpired(): bool
    {
        $scheduleDateTime = \Carbon\Carbon::parse($this->date . ' ' . $this->start_time);
        return $scheduleDateTime->isPast();
    }

    /**
     * Verifica si el horario está ocupado (tiene una cita confirmada)
     */
    public function isOccupied(): bool
    {
        return $this->appointments()
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();
    }

    /**
     * Verifica si el horario está realmente disponible
     * (disponible, no expirado y no ocupado)
     */
    public function isReallyAvailable(): bool
    {
        return $this->is_available && !$this->isExpired() && !$this->isOccupied();
    }

    /**
     * Marca el horario como no disponible
     */
    public function markAsUnavailable(): void
    {
        $this->update(['is_available' => false]);
    }

    /**
     * Scope para obtener solo horarios realmente disponibles
     */
    public function scopeReallyAvailable($query)
    {
        $now = \Carbon\Carbon::now();

        return $query->where('is_available', true)
            ->where(function ($subQuery) use ($now) {
                $subQuery->where('date', '>', $now->format('Y-m-d'))
                    ->orWhere(function ($timeQuery) use ($now) {
                        $timeQuery->where('date', '=', $now->format('Y-m-d'))
                            ->whereRaw("CONCAT(date, ' ', start_time) > ?", [$now->format('Y-m-d H:i:s')]);
                    });
            })
            ->whereDoesntHave('appointments', function ($appointmentQuery) {
                $appointmentQuery->whereIn('status', ['pending', 'confirmed']);
            });
    }
}
