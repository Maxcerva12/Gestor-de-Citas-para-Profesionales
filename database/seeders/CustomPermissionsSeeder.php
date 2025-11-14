<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Config;
use BezhanSalleh\FilamentShield\Support\Utils;

class CustomPermissionsSeeder extends Seeder
{
    /**
     * Genera los permisos personalizados definidos en filament-shield.php
     */
    public function run(): void
    {
        $this->command->info('🔐 Iniciando generación de permisos personalizados...');

        // Obtener los custom permissions desde la configuración
        $customPermissions = Config::get('filament-shield.custom_permissions', []);

        if (empty($customPermissions)) {
            $this->command->warn('⚠️  No se encontraron custom permissions en la configuración.');
            return;
        }

        $guardName = Utils::getFilamentAuthGuard();
        $createdCount = 0;
        $existingCount = 0;

        // Crear cada permiso personalizado
        foreach ($customPermissions as $permission) {
            $permissionModel = Permission::firstOrCreate(
                [
                    'name' => $permission,
                    'guard_name' => $guardName,
                ]
            );

            if ($permissionModel->wasRecentlyCreated) {
                $createdCount++;
                $this->command->info("✅ Permiso creado: {$permission}");
            } else {
                $existingCount++;
                $this->command->info("ℹ️  Permiso ya existe: {$permission}");
            }
        }

        // Asignar permisos al super_admin si está habilitado
        if (Utils::isSuperAdminEnabled()) {
            $superAdminRole = Role::where('name', Utils::getSuperAdminName())->first();
            
            if ($superAdminRole) {
                $superAdminRole->givePermissionTo($customPermissions);
                $this->command->info("🛡️  Permisos asignados al rol: {$superAdminRole->name}");
            } else {
                $this->command->warn("⚠️  No se encontró el rol super_admin");
            }
        }

        // Limpiar caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->newLine();
        $this->command->info("📊 Resumen:");
        $this->command->info("   • Permisos creados: {$createdCount}");
        $this->command->info("   • Permisos existentes: {$existingCount}");
        $this->command->info("   • Total procesados: " . count($customPermissions));
        $this->command->newLine();
        $this->command->info('✨ ¡Generación de permisos personalizados completada!');
    }
}
