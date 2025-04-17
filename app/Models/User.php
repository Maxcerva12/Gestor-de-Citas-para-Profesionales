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

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, Authorizable;
    use Billable;
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url',
        'document_type',
        'document_number',
        'document',
        'phone',
        'address',
        'city',
        'country',
        'profession',
        'especialty',
        'description',
        'custom_fields',
        'google_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'custom_fields' => 'array',
            'google_token' => 'json',
        ];
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'google_token' => 'json',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        // Solo verifica si el usuario tiene algún rol, no da permisos extra
        return $this->roles()->count() > 0;
    }
    /**
     * Relación con los horarios del profesional.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
    /**
     * Relación con los servicios del profesional.
     */

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

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
