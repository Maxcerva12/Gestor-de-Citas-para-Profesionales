<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ClinicalDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_history_id',
        'appointment_id',
        'tipo_documento',
        'nombre_documento',
        'descripcion',
        'archivo_path',
        'archivo_nombre_original',
        'archivo_mime_type',
        'archivo_size',
        'fecha_documento',
        'subido_por',
        'observaciones',
    ];

    protected $casts = [
        'fecha_documento' => 'date',
        'archivo_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Tipos de documentos disponibles
    public const TIPOS_DOCUMENTO = [
        'radiografia_panoramica' => 'Radiografía Panorámica',
        'radiografia_periapical' => 'Radiografía Periapical',
        'radiografia_bite_wing' => 'Radiografía Bite-Wing',
        'tomografia' => 'Tomografía',
        'fotografia_intraoral' => 'Fotografía Intraoral',
        'fotografia_extraoral' => 'Fotografía Extraoral',
        'consentimiento_informado' => 'Consentimiento Informado',
        'consentimiento_cirugia' => 'Consentimiento Cirugía',
        'consentimiento_endodoncia' => 'Consentimiento Endodoncia',
        'consentimiento_ortodoncia' => 'Consentimiento Ortodoncia',
        'consentimiento_fluor' => 'Consentimiento Flúor (Menores)',
        'laboratorio' => 'Resultado de Laboratorio',
        'receta' => 'Receta Médica',
        'formula_medica' => 'Fórmula Médica',
        'orden_examen' => 'Orden de Examen',
        'incapacidad' => 'Incapacidad',
        'remision' => 'Remisión',
        'historia_clinica_externa' => 'Historia Clínica Externa',
        'otro' => 'Otro',
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
     * Relación con el usuario que subió el documento
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subido_por');
    }

    /**
     * Obtener la URL del archivo
     */
    public function getFileUrlAttribute(): ?string
    {
        if ($this->archivo_path) {
            return Storage::url($this->archivo_path);
        }
        return null;
    }

    /**
     * Verificar si es una imagen
     */
    public function getIsImageAttribute(): bool
    {
        $imageTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        return in_array($this->archivo_mime_type, $imageTypes);
    }

    /**
     * Obtener el tamaño del archivo en formato legible
     */
    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->archivo_size;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Boot method para eventos del modelo
     */
    protected static function boot()
    {
        parent::boot();

        // Al crear, asignar automáticamente el usuario que sube
        static::creating(function ($model) {
            if (auth()->check() && !$model->subido_por) {
                $model->subido_por = auth()->id();
            }

            // Si no tiene fecha, asignar la actual
            if (!$model->fecha_documento) {
                $model->fecha_documento = now();
            }
        });

        // Al eliminar, borrar el archivo físico
        static::deleting(function ($model) {
            if ($model->archivo_path && Storage::exists($model->archivo_path)) {
                Storage::delete($model->archivo_path);
            }
        });
    }
}
