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
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class Client extends Authenticatable implements FilamentUser, HasAvatar, CanResetPassword, MustVerifyEmail
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
        // Campos de identificación
        'tipo_documento',
        'numero_documento',
        'genero',
        'ocupacion',
        'fecha_nacimiento',
        // Información de seguro y contacto de emergencia
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


}
