<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        // Datos Generales de Salud
        'motivo_consulta',
        'enfermedad_actual',
        'antecedentes_personales',
        'antecedentes_familiares',
        'habitos',
        'medicamentos_actuales',
        'alergias_medicamentos',
        'cirugias_previas',
        'hospitalizaciones',
        'transfusiones',
        'enfermedades_cronicas',

        // Antecedentes Odontológicos
        'ultima_visita_odontologo',
        'motivo_ultima_visita',
        'tratamientos_previos',
        'experiencias_traumaticas',
        'higiene_oral_frecuencia',
        'sangrado_encias',
        'sensibilidad_dental',
        'bruxismo',
        'ortodoncia_previa',

        // Odontograma - se mantiene aquí para la gestión clínica
        'odontogram',
        'odontogram_observations',
        'odontogram_last_update',

        // Información Adicional
        'observaciones_generales',
        'plan_tratamiento',
        'diagnostico_principal',
        'pronostico',
        'consentimiento_informado',

        // Metadatos
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'odontogram' => 'array',
        'ultima_visita_odontologo' => 'date',
        'odontogram_last_update' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con el cliente (paciente)
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relación con las notas de evolución
     */
    public function evolutionNotes(): HasMany
    {
        return $this->hasMany(EvolutionNote::class)->orderBy('fecha_nota', 'desc');
    }

    /**
     * Relación con los documentos clínicos
     */
    public function clinicalDocuments(): HasMany
    {
        return $this->hasMany(ClinicalDocument::class)->orderBy('fecha_documento', 'desc');
    }

    /**
     * Relación con el usuario que creó el registro
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relación con el usuario que actualizó el registro
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Verificar si tiene odontograma con datos
     */
    public function getHasOdontogramAttribute(): bool
    {
        if (!$this->odontogram || empty($this->odontogram)) {
            return false;
        }

        // Verificar si hay datos reales en permanent, temporary o mixed
        $permanent = $this->odontogram['permanent'] ?? [];
        $temporary = $this->odontogram['temporary'] ?? [];
        $mixed = $this->odontogram['mixed'] ?? [];

        foreach ([$permanent, $temporary, $mixed] as $section) {
            if (!empty($section)) {
                foreach ($section as $tooth) {
                    // Verificar si tiene faces con datos, o conditions/treatments
                    if (
                        !empty($tooth['faces']) ||
                        !empty($tooth['conditions']) ||
                        !empty($tooth['treatments'])
                    ) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Obtener el nombre completo del paciente
     */
    public function getPatientNameAttribute(): string
    {
        return $this->client->name . ' ' . $this->client->apellido;
    }

    /**
     * Boot method para eventos del modelo
     */
    protected static function boot()
    {
        parent::boot();

        // Al crear, asignar el usuario que crea
        static::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by = auth()->id();
            }
        });

        // Al actualizar, asignar el usuario que actualiza
        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });
    }
}
