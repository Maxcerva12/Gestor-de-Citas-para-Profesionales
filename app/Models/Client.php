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
use Laravel\Cashier\Billable;

class Client extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, Authorizable;
    use Billable;

    protected $fillable = [
        'name',
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
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed', // Cifra la contraseña automáticamente
        'active' => 'boolean',
        'custom_fields' => 'array',
        'odontogram' => 'array',
        'last_dental_visit' => 'date',
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
        return $this->odontogram && !empty($this->odontogram);
    }
}
