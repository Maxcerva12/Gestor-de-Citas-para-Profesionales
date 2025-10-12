<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Illuminate\Support\Collection;

class RoleManagerService
{
    /**
     * Definición central de todas las categorías de roles
     */
    public static function getRoleCategories(): array
    {
        return [
            'Personal Odontológico' => [
                'odontologo-general' => 'Odontólogo General',
                'ortodoncista' => 'Ortodoncista',
                'endodoncista' => 'Endodoncista',
                'periodoncista' => 'Periodoncista',
                'cirujano-oral' => 'Cirujano Oral',
                'protesista' => 'Protesista Dental',
                'implantologo' => 'Implantólogo',
                'higienista-dental' => 'Higienista Dental',
                'auxiliar-odontologia' => 'Auxiliar de Odontología',
            ],
            'Personal Administrativo' => [
                'recepcionista' => 'Recepcionista',
                'secretaria' => 'Secretaria',
                'asesor-comercial' => 'Asesor Comercial',
                'contador' => 'Contador',
            ],
            'Gerencia' => [
                'gerencia' => 'Gerencia',
                'coordinador-clinica' => 'Coordinador de Clínica',
            ],
        ];
    }

    /**
     * Obtiene un mapeo plano de todos los roles con sus nombres bonitos
     */
    public static function getAllRolesPrettyNames(): array
    {
        $categories = static::getRoleCategories();
        $prettyNames = [];

        foreach ($categories as $category => $roles) {
            foreach ($roles as $roleName => $roleLabel) {
                $prettyNames[$roleName] = $roleLabel;
            }
        }

        // Agregar algunos roles adicionales que podrían existir
        $prettyNames['usuario'] = 'Usuario';

        return $prettyNames;
    }

    /**
     * Obtiene los nombres de roles para una categoría específica
     */
    public static function getRoleNamesForCategory(string $category): array
    {
        $categories = static::getRoleCategories();
        return array_keys($categories[$category] ?? []);
    }

    /**
     * Obtiene todos los roles categorizados (excluyendo 'Otros')
     */
    public static function getCategorizedRoleNames(): array
    {
        $categories = static::getRoleCategories();
        $allRoles = [];

        foreach ($categories as $category => $roles) {
            $allRoles = array_merge($allRoles, array_keys($roles));
        }

        return $allRoles;
    }

    /**
     * Obtiene las opciones agrupadas para formularios de Filament
     */
    public static function getGroupedOptionsForFilament(): array
    {
        $categories = static::getRoleCategories();
        $groupedOptions = [];

        foreach ($categories as $categoryName => $roles) {
            $groupedOptions[$categoryName] = $roles;
        }

        // Agregar categoría "Otros" si hay roles no categorizados
        $rolesByName = Role::all()->keyBy('name');
        $categorizedRoleNames = static::getCategorizedRoleNames();
        $uncategorizedRoles = $rolesByName->whereNotIn('name', $categorizedRoleNames);

        if ($uncategorizedRoles->isNotEmpty()) {
            $prettyNames = static::getAllRolesPrettyNames();
            foreach ($uncategorizedRoles as $role) {
                $groupedOptions['Otros'][$role->name] = $prettyNames[$role->name] ?? $role->name;
            }
        }

        return $groupedOptions;
    }

    /**
     * Obtiene el nombre bonito de un rol
     */
    public static function getPrettyName(string $roleName): string
    {
        $prettyNames = static::getAllRolesPrettyNames();
        return $prettyNames[$roleName] ?? ucwords(str_replace('-', ' ', $roleName));
    }

    /**
     * Determina a qué categoría pertenece un rol
     */
    public static function getCategoryForRole(string $roleName): ?string
    {
        $categories = static::getRoleCategories();

        foreach ($categories as $categoryName => $roles) {
            if (array_key_exists($roleName, $roles)) {
                return $categoryName;
            }
        }

        return 'Otros'; // Si no está categorizado
    }

    /**
     * Verifica si un rol está categorizado
     */
    public static function isRoleCategorized(string $roleName): bool
    {
        return in_array($roleName, static::getCategorizedRoleNames());
    }
}