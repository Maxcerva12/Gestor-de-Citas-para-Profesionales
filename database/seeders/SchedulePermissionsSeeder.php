<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class SchedulePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Iniciando creación de permisos para gestión de horarios...');

        // Lista de permisos a crear
        $permissions = [
            'view_schedule_calendar',
            'manage_schedules',
            'view_all_schedules',
            'edit_all_schedules',
            'delete_all_schedules',
        ];

        // Crear los permisos si no existen
        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
            $this->command->info("Permiso creado o verificado: {$permissionName}");
        }

        // Asignar permisos al rol super_admin
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permissions);
            $this->command->info('Permisos asignados al rol super_admin');
        } else {
            $this->command->warn('No se encontró el rol super_admin');
        }

        // Asignar permisos básicos al rol professional
        $professionalRole = Role::where('name', 'professional')->first();
        if ($professionalRole) {
            $professionalRole->givePermissionTo(['view_schedule_calendar', 'manage_schedules']);
            $this->command->info('Permisos básicos asignados al rol professional');
        } else {
            $this->command->warn('No se encontró el rol professional');
        }

        $this->command->info('¡Creación de permisos completada!');
    }
}
//Para correr este seeder, usa el siguiente comando en la terminal:
// php artisan db:seed --class=SchedulePermissionsSeeder

// si prefieres hacerlo con tinker, puedes usar:
// php artisan tinker
// y el siguiente codigo:

// $permissions = [
//     'view_schedule_calendar',
//     'manage_schedules',
//     'view_all_schedules',
//     'edit_all_schedules',
//     'delete_all_schedules',
// ];

// foreach ($permissions as $permissionName) {
//     \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
//     echo "Permiso creado o verificado: {$permissionName}\n";
// }

// $superAdminRole = \Spatie\Permission\Models\Role::where('name', 'super_admin')->first();
// if ($superAdminRole) {
//     $superAdminRole->givePermissionTo($permissions);
//     echo "Permisos asignados al rol super_admin\n";
// }

// $professionalRole = \Spatie\Permission\Models\Role::where('name', 'professional')->first();
// if ($professionalRole) {
//     $professionalRole->givePermissionTo(['view_schedule_calendar', 'manage_schedules']);
//     echo "Permisos básicos asignados al rol professional\n";
// }