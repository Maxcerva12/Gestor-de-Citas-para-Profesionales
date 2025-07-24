<?php

namespace App\Services;

class OdontogramService
{
    /**
     * Sistema de numeración FDI para dientes permanentes
     */
    public const PERMANENT_TEETH = [
        // Cuadrante 1 (Superior derecho)
        11,
        12,
        13,
        14,
        15,
        16,
        17,
        18,
        // Cuadrante 2 (Superior izquierdo)
        21,
        22,
        23,
        24,
        25,
        26,
        27,
        28,
        // Cuadrante 3 (Inferior izquierdo)
        31,
        32,
        33,
        34,
        35,
        36,
        37,
        38,
        // Cuadrante 4 (Inferior derecho)
        41,
        42,
        43,
        44,
        45,
        46,
        47,
        48,
    ];

    /**
     * Sistema de numeración FDI para dientes temporales/deciduos
     */
    public const TEMPORARY_TEETH = [
        // Cuadrante 5 (Superior derecho)
        51,
        52,
        53,
        54,
        55,
        // Cuadrante 6 (Superior izquierdo)
        61,
        62,
        63,
        64,
        65,
        // Cuadrante 7 (Inferior izquierdo)
        71,
        72,
        73,
        74,
        75,
        // Cuadrante 8 (Inferior derecho)
        81,
        82,
        83,
        84,
        85,
    ];

    /**
     * Estados posibles de los dientes
     */
    public const TOOTH_STATUSES = [
        'healthy' => ['label' => 'Sano', 'color' => '#10B981', 'description' => 'Diente en estado saludable'],
        'cavity' => ['label' => 'Caries', 'color' => '#EF4444', 'description' => 'Presenta caries dental'],
        'treated' => ['label' => 'Tratado', 'color' => '#3B82F6', 'description' => 'Tratamiento realizado'],
        'missing' => ['label' => 'Ausente', 'color' => '#6B7280', 'description' => 'Diente ausente'],
        'implant' => ['label' => 'Implante', 'color' => '#8B5CF6', 'description' => 'Implante dental'],
        'crown' => ['label' => 'Corona', 'color' => '#F59E0B', 'description' => 'Corona dental'],
        'root_canal' => ['label' => 'Endodoncia', 'color' => '#EC4899', 'description' => 'Tratamiento de conducto'],
    ];

    /**
     * Inicializar un odontograma vacío
     */
    public static function initializeEmptyOdontogram(): array
    {
        return [
            'permanent' => [],
            'temporary' => [],
            'metadata' => [
                'created_at' => now()->toISOString(),
                'last_updated' => now()->toISOString(),
                'version' => '1.0'
            ]
        ];
    }

    /**
     * Obtener información de un diente específico
     */
    public static function getToothInfo(int $toothNumber): array
    {
        $quadrant = intval($toothNumber / 10);
        $position = $toothNumber % 10;

        $quadrantNames = [
            1 => 'Superior Derecho',
            2 => 'Superior Izquierdo',
            3 => 'Inferior Izquierdo',
            4 => 'Inferior Derecho',
            5 => 'Superior Derecho (Temporal)',
            6 => 'Superior Izquierdo (Temporal)',
            7 => 'Inferior Izquierdo (Temporal)',
            8 => 'Inferior Derecho (Temporal)',
        ];

        $toothTypes = [
            1 => 'Incisivo Central',
            2 => 'Incisivo Lateral',
            3 => 'Canino',
            4 => 'Primer Premolar',
            5 => 'Segundo Premolar',
            6 => 'Primer Molar',
            7 => 'Segundo Molar',
            8 => 'Tercer Molar (Muela del Juicio)',
        ];

        // Para dientes temporales, ajustar los tipos
        if ($quadrant >= 5) {
            $toothTypes = [
                1 => 'Incisivo Central',
                2 => 'Incisivo Lateral',
                3 => 'Canino',
                4 => 'Primer Molar',
                5 => 'Segundo Molar',
            ];
        }

        return [
            'number' => $toothNumber,
            'quadrant' => $quadrant,
            'position' => $position,
            'quadrant_name' => $quadrantNames[$quadrant] ?? 'Desconocido',
            'type' => $toothTypes[$position] ?? 'Desconocido',
            'is_permanent' => $quadrant <= 4,
            'is_temporary' => $quadrant >= 5,
        ];
    }

    /**
     * Validar estructura del odontograma
     */
    public static function validateOdontogram(array $odontogram): array
    {
        $errors = [];

        if (!isset($odontogram['permanent']) || !is_array($odontogram['permanent'])) {
            $errors[] = 'La sección de dientes permanentes es requerida';
        }

        if (!isset($odontogram['temporary']) || !is_array($odontogram['temporary'])) {
            $errors[] = 'La sección de dientes temporales es requerida';
        }

        // Validar números de dientes permanentes
        if (isset($odontogram['permanent'])) {
            foreach ($odontogram['permanent'] as $toothNumber => $data) {
                if (!in_array((int) $toothNumber, self::PERMANENT_TEETH)) {
                    $errors[] = "Número de diente permanente inválido: {$toothNumber}";
                }

                if (isset($data['status']) && !array_key_exists($data['status'], self::TOOTH_STATUSES)) {
                    $errors[] = "Estado de diente inválido: {$data['status']} para diente {$toothNumber}";
                }
            }
        }

        // Validar números de dientes temporales
        if (isset($odontogram['temporary'])) {
            foreach ($odontogram['temporary'] as $toothNumber => $data) {
                if (!in_array((int) $toothNumber, self::TEMPORARY_TEETH)) {
                    $errors[] = "Número de diente temporal inválido: {$toothNumber}";
                }

                if (isset($data['status']) && !array_key_exists($data['status'], self::TOOTH_STATUSES)) {
                    $errors[] = "Estado de diente inválido: {$data['status']} para diente {$toothNumber}";
                }
            }
        }

        return $errors;
    }

    /**
     * Generar estadísticas del odontograma
     */
    public static function generateStatistics(array $odontogram): array
    {
        $stats = [
            'total_permanent' => count($odontogram['permanent'] ?? []),
            'total_temporary' => count($odontogram['temporary'] ?? []),
            'status_counts' => []
        ];

        // Inicializar contadores de estado
        foreach (self::TOOTH_STATUSES as $status => $config) {
            $stats['status_counts'][$status] = 0;
        }

        // Contar estados en dientes permanentes
        foreach ($odontogram['permanent'] ?? [] as $data) {
            $status = $data['status'] ?? 'healthy';
            if (isset($stats['status_counts'][$status])) {
                $stats['status_counts'][$status]++;
            }
        }

        // Contar estados en dientes temporales
        foreach ($odontogram['temporary'] ?? [] as $data) {
            $status = $data['status'] ?? 'healthy';
            if (isset($stats['status_counts'][$status])) {
                $stats['status_counts'][$status]++;
            }
        }

        return $stats;
    }

    /**
     * Exportar odontograma a diferentes formatos
     */
    public static function export(array $odontogram, string $format = 'json'): string
    {
        switch ($format) {
            case 'json':
                return json_encode($odontogram, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            case 'csv':
                $csv = "Diente,Tipo,Estado,Notas,Fecha Actualización\n";

                foreach ($odontogram['permanent'] ?? [] as $number => $data) {
                    $info = self::getToothInfo((int) $number);
                    $csv .= sprintf(
                        "%d,%s,%s,%s,%s\n",
                        $number,
                        $info['type'],
                        $data['status'] ?? 'healthy',
                        $data['notes'] ?? '',
                        $data['updatedAt'] ?? ''
                    );
                }

                foreach ($odontogram['temporary'] ?? [] as $number => $data) {
                    $info = self::getToothInfo((int) $number);
                    $csv .= sprintf(
                        "%d,%s,%s,%s,%s\n",
                        $number,
                        $info['type'],
                        $data['status'] ?? 'healthy',
                        $data['notes'] ?? '',
                        $data['updatedAt'] ?? ''
                    );
                }

                return $csv;

            default:
                throw new \InvalidArgumentException("Formato de exportación no soportado: {$format}");
        }
    }
}
