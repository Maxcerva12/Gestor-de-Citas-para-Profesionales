<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, Authorizable;

    protected $fillable = [
        'name',
        'apellido',
        'email',
        'password',
        'phone',
        'address',
        'city',
        'country',
        'avatar_url',
        'active',
        'custom_fields',
        'notes',
        'odontogram',
        'dental_notes',
        'last_dental_visit',
        // Nuevos campos médicos
        'tipo_documento',
        'numero_documento',
        'genero',
        'fecha_nacimiento',
        'tipo_sangre',
        'historial_medico',
        'alergias',
        'aseguradora',
        'nombre_contacto_emergencia',
        'telefono_contacto_emergencia',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'active' => 'boolean',
        'custom_fields' => 'array',
        'odontogram' => 'array',
        'last_dental_visit' => 'datetime',
        'fecha_nacimiento' => 'datetime',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        // Solo permite acceso al panel 'client'
        return $panel->getId() === 'client';
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function medicalHistory()
    {
        return $this->hasOne(MedicalHistory::class);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if ($this->avatar_url) {
            // Normalizar la ruta para Windows
            $path = str_replace('\\', '/', $this->avatar_url);

            // Asegurarse de que la ruta comience correctamente
            if (!str_starts_with($path, 'avatars/')) {
                $path = 'avatars/' . basename($path);
            }

            // Usar asset() para generar la URL completa
            return asset('storage/' . $path);
        }
        return null;
    }

    public function getHasOdontogramAttribute(): bool
    {
        if (!$this->odontogram || empty($this->odontogram)) {
            return false;
        }

        // Verificar si hay datos reales en permanent, temporary o mixed
        $permanent = $this->odontogram['permanent'] ?? [];
        $temporary = $this->odontogram['temporary'] ?? [];
        $mixed = $this->odontogram['mixed'] ?? [];

        // Para permanent: verificar que no sea un array vacío
        $hasPermanentData = is_array($permanent) && !empty($permanent);

        // Para temporary: verificar que no sea un array vacío
        $hasTemporaryData = is_array($temporary) && !empty($temporary);

        // Para mixed: verificar que no sea un array vacío
        $hasMixedData = is_array($mixed) && !empty($mixed);

        // Si permanent es un array indexado (como [null, null, {...}, null])
        // verificar que al menos un elemento no sea null
        if ($hasPermanentData && array_is_list($permanent)) {
            $hasPermanentData = !empty(array_filter($permanent, fn($item) => $item !== null));
        }

        // Si temporary es un array indexado, hacer la misma verificación
        if ($hasTemporaryData && array_is_list($temporary)) {
            $hasTemporaryData = !empty(array_filter($temporary, fn($item) => $item !== null));
        }

        // Si mixed es un array indexado, hacer la misma verificación
        if ($hasMixedData && array_is_list($mixed)) {
            $hasMixedData = !empty(array_filter($mixed, fn($item) => $item !== null));
        }

        return $hasPermanentData || $hasTemporaryData || $hasMixedData;
    }
}
