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
        'tipo_sangre',
        'alergias',
        'antecedentes_personales',
        'antecedentes_familiares',
        'habitos',
        'medicamentos_actuales',
        'alergias_medicamentos',
        'cirugias_previas',
        'hospitalizaciones',
        'transfusiones',
        'enfermedades_cronicas',
        'historial_observaciones',

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
        
        // Datos de Anamnesis
        'anamnesis_basica',
        
        // Examen Físico Estomatológico
        'examen_fisico_estomatologico',
        
        // Examen Dental
        'examen_dental',
        
        // Evaluación del Estado Periodontal
        'evaluacion_periodontal',

        // Metadatos
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'odontogram' => 'array',
        'anamnesis_basica' => 'array',
        'examen_fisico_estomatologico' => 'array',
        'examen_dental' => 'array',
        'evaluacion_periodontal' => 'array',
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
     * Obtener datos de anamnesis básica
     */
    public function getAnamnesisData(?string $key = null)
    {
        $data = $this->anamnesis_basica ?? [];
        
        if ($key !== null) {
            return $data[$key] ?? null;
        }
        
        return $data;
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

        // Verificar si hay datos reales en permanent, temporary, temporal (ambas formas), o mixed
        $permanent = $this->odontogram['permanent'] ?? [];
        $temporary = $this->odontogram['temporary'] ?? [];
        $temporal = $this->odontogram['temporal'] ?? []; // Agregar soporte para 'temporal'
        $mixed = $this->odontogram['mixed'] ?? [];

        foreach ([$permanent, $temporary, $temporal, $mixed] as $section) {
            if (!empty($section)) {
                // Si es un array indexado (formato antiguo), verificar cada elemento
                if (is_array($section) && isset($section[0])) {
                    foreach ($section as $tooth) {
                        if (
                            $tooth !== null && (
                                !empty($tooth['faces']) ||
                                !empty($tooth['conditions']) ||
                                !empty($tooth['treatments'])
                            )
                        ) {
                            return true;
                        }
                    }
                } else {
                    // Si es un objeto/array asociativo (formato nuevo), verificar cada diente
                    foreach ($section as $toothNumber => $tooth) {
                        if (
                            $tooth !== null && (
                                !empty($tooth['faces']) ||
                                !empty($tooth['conditions']) ||
                                !empty($tooth['treatments'])
                            )
                        ) {
                            return true;
                        }
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
