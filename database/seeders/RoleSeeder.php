<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            // Personal Odontológico
            'odontologo-general',
            'ortodoncista',
            'endodoncista',
            'periodoncista',
            'cirujano-oral',
            'higienista-dental',
            'auxiliar-odontologia',
            // Personal Administrativo
            'recepcionista',
            'secretaria',
            'asesor-comercial',
            'contador',
            // Gerencia
            'gerencia',
            'coordinador-clinica',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}