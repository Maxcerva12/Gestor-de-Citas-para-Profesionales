<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class InvoiceSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    protected $casts = [
        // Removemos el cast json porque estamos guardando strings simples
    ];

    /**
     * Obtener una configuración por clave
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("invoice_settings.{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            if (!$setting) {
                return $default;
            }

            $value = $setting->value;

            // Si el valor es un JSON string (empezar con { [ o "), decodificarlo
            if (is_string($value)) {
                // Verificar si es JSON válido
                if (str_starts_with($value, '"') && str_ends_with($value, '"')) {
                    // Es un string JSON, decodificar
                    $decoded = json_decode($value, true);
                    return $decoded !== null ? $decoded : $value;
                } elseif (str_starts_with($value, '{') || str_starts_with($value, '[')) {
                    // Es un objeto/array JSON, decodificar
                    $decoded = json_decode($value, true);
                    return $decoded !== null ? $decoded : $value;
                }
            }

            return $value ?? $default;
        });
    }

    /**
     * Establecer una configuración
     */
    public static function set(string $key, $value, string $type = 'string', string $description = null): void
    {
        // Para strings simples, no necesitamos JSON encoding
        $finalValue = $value;

        // Solo hacer JSON encode si es un array o objeto
        if (is_array($value) || is_object($value)) {
            $finalValue = json_encode($value);
        }

        static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $finalValue,
                'type' => $type,
                'description' => $description,
            ]
        );

        // Limpiar caché específica y general
        Cache::forget("invoice_settings.{$key}");
        Cache::forget('invoice_settings.all');

        // Forzar limpieza de toda la caché relacionada
        if (in_array($key, ['pdf_template_color', 'pdf_font', 'invoice_template'])) {
            Cache::flush();
        }
    }

    /**
     * Obtener información de la empresa
     */
    public static function getCompanyInfo(): array
    {
        return [
            'company' => self::get('company_name', 'Mi Empresa'),
            'name' => self::get('company_name', 'Mi Empresa'), // Para el campo name del vendedor
            'email' => self::get('company_email', 'email@empresa.com'),
            'phone' => self::get('company_phone', '+57 123 456 7890'),
            'tax_number' => self::get('company_tax_number', '900123456-1'),
            'address' => [
                'street' => self::get('company_address_street', 'Calle Ejemplo 123'),
                'city' => self::get('company_address_city', 'Bogotá'),
                'state' => self::get('company_address_state', 'Cundinamarca'),
                'postal_code' => self::get('company_address_postal_code', '110111'),
                'country' => self::get('company_address_country', 'Colombia'),
            ],
            'fields' => [
                'Régimen Fiscal' => 'Común',
                'Actividad Económica' => 'Servicios Profesionales',
            ],
        ];
    }

    /**
     * Obtener el logo de la empresa
     */
    public static function getCompanyLogo(): ?string
    {
        $logoPath = static::get('company_logo');

        if (!$logoPath) {
            return null;
        }

        // Si es una ruta completa de storage
        if (Storage::disk('public')->exists($logoPath)) {
            $fullPath = Storage::disk('public')->path($logoPath);
            $mimeType = mime_content_type($fullPath);
            $logoData = "data:{$mimeType};base64," . base64_encode(file_get_contents($fullPath));
            return $logoData;
        }

        return null;
    }

    /**
     * Obtener todas las configuraciones como array
     */
    public static function getAllSettings(): array
    {
        return Cache::remember('invoice_settings.all', 3600, function () {
            return static::all()->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Limpiar toda la caché de configuraciones
     */
    public static function clearCache(): void
    {
        $settings = static::all();
        foreach ($settings as $setting) {
            Cache::forget("invoice_settings.{$setting->key}");
        }
        Cache::forget('invoice_settings.all');
    }
}
