<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvolutionNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_history_id',
        'appointment_id',
        'fecha_nota',
        'motivo_consulta',
        'sintomas',
        'examen_clinico',
        'diagnostico',
        'tratamiento_realizado',
        'medicamentos_recetados',
        'indicaciones',
        'proxima_cita',
        'observaciones',
        'profesional_id',
        'profesional_nombre',
    ];

    protected $casts = [
        'fecha_nota' => 'datetime',
        'proxima_cita' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con la historia clínica
     */
    public function medicalHistory(): BelongsTo
    {
        return $this->belongsTo(MedicalHistory::class);
    }

    /**
     * Relación con la cita (si aplica)
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Relación con el profesional que registró la nota
     */
    public function professional(): BelongsTo
    {
        return $this->belongsTo(User::class, 'profesional_id');
    }

    /**
     * Boot method para eventos del modelo
     */
    protected static function boot()
    {
        parent::boot();

        // Al crear, asignar automáticamente el profesional actual
        static::creating(function ($model) {
            if (auth()->check() && !$model->profesional_id) {
                $model->profesional_id = auth()->id();
                $model->profesional_nombre = auth()->user()->name;
            }

            // Si no tiene fecha, asignar la actual
            if (!$model->fecha_nota) {
                $model->fecha_nota = now();
            }
        });
    }
}
