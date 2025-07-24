<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Services\OdontogramService;

class OdontogramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Poblando odontogramas de ejemplo...');

        // Obtener clientes existentes o crear algunos
        $clients = Client::take(5)->get();

        if ($clients->count() === 0) {
            $this->command->info('No hay clientes disponibles. Creando algunos clientes de prueba...');

            $testClients = [
                ['name' => 'Juan Pérez', 'email' => 'juan@example.com', 'phone' => '+57123456789'],
                ['name' => 'María García', 'email' => 'maria@example.com', 'phone' => '+57234567890'],
                ['name' => 'Carlos López', 'email' => 'carlos@example.com', 'phone' => '+57345678901'],
                ['name' => 'Ana Rodríguez', 'email' => 'ana@example.com', 'phone' => '+57456789012'],
                ['name' => 'Luis Martínez', 'email' => 'luis@example.com', 'phone' => '+57567890123'],
            ];

            foreach ($testClients as $clientData) {
                Client::create(array_merge($clientData, [
                    'password' => bcrypt('password'),
                    'address' => 'Dirección de ejemplo',
                    'city' => 'Bogotá',
                    'country' => 'Colombia',
                ]));
            }

            $clients = Client::take(5)->get();
        }

        $odontogramExamples = [
            // Cliente 1: Dentición saludable con algunas caries
            [
                'permanent' => [
                    '11' => ['status' => 'healthy', 'notes' => '', 'updatedAt' => now()->subDays(30)->toISOString()],
                    '12' => ['status' => 'cavity', 'notes' => 'Caries superficial', 'updatedAt' => now()->subDays(15)->toISOString()],
                    '13' => ['status' => 'healthy', 'notes' => '', 'updatedAt' => now()->subDays(30)->toISOString()],
                    '21' => ['status' => 'treated', 'notes' => 'Resina compuesta', 'updatedAt' => now()->subDays(60)->toISOString()],
                    '22' => ['status' => 'healthy', 'notes' => '', 'updatedAt' => now()->subDays(30)->toISOString()],
                    '31' => ['status' => 'cavity', 'notes' => 'Caries profunda', 'updatedAt' => now()->subDays(10)->toISOString()],
                    '36' => ['status' => 'crown', 'notes' => 'Corona de porcelana', 'updatedAt' => now()->subDays(180)->toISOString()],
                    '46' => ['status' => 'root_canal', 'notes' => 'Endodoncia completa', 'updatedAt' => now()->subDays(90)->toISOString()],
                ],
                'temporary' => [],
                'metadata' => [
                    'created_at' => now()->subDays(365)->toISOString(),
                    'last_updated' => now()->subDays(10)->toISOString(),
                    'version' => '1.0'
                ]
            ],

            // Cliente 2: Caso con implantes y tratamientos avanzados
            [
                'permanent' => [
                    '11' => ['status' => 'crown', 'notes' => 'Corona metal-cerámica', 'updatedAt' => now()->subDays(120)->toISOString()],
                    '12' => ['status' => 'healthy', 'notes' => '', 'updatedAt' => now()->subDays(30)->toISOString()],
                    '16' => ['status' => 'missing', 'notes' => 'Extracción por fractura', 'updatedAt' => now()->subDays(200)->toISOString()],
                    '17' => ['status' => 'implant', 'notes' => 'Implante titanio + corona', 'updatedAt' => now()->subDays(150)->toISOString()],
                    '26' => ['status' => 'root_canal', 'notes' => 'Endodoncia + incrustación', 'updatedAt' => now()->subDays(100)->toISOString()],
                    '36' => ['status' => 'missing', 'notes' => 'Agenesia dental', 'updatedAt' => now()->subDays(365)->toISOString()],
                    '37' => ['status' => 'implant', 'notes' => 'Implante con carga inmediata', 'updatedAt' => now()->subDays(180)->toISOString()],
                    '46' => ['status' => 'treated', 'notes' => 'Amalgama grande', 'updatedAt' => now()->subDays(300)->toISOString()],
                ],
                'temporary' => [],
                'metadata' => [
                    'created_at' => now()->subDays(400)->toISOString(),
                    'last_updated' => now()->subDays(30)->toISOString(),
                    'version' => '1.0'
                ]
            ],

            // Cliente 3: Dentición temporal (niño)
            [
                'permanent' => [],
                'temporary' => [
                    '51' => ['status' => 'healthy', 'notes' => '', 'updatedAt' => now()->subDays(30)->toISOString()],
                    '52' => ['status' => 'healthy', 'notes' => '', 'updatedAt' => now()->subDays(30)->toISOString()],
                    '53' => ['status' => 'cavity', 'notes' => 'Caries inicial', 'updatedAt' => now()->subDays(15)->toISOString()],
                    '54' => ['status' => 'treated', 'notes' => 'Resina compuesta', 'updatedAt' => now()->subDays(45)->toISOString()],
                    '55' => ['status' => 'healthy', 'notes' => '', 'updatedAt' => now()->subDays(30)->toISOString()],
                    '61' => ['status' => 'healthy', 'notes' => '', 'updatedAt' => now()->subDays(30)->toISOString()],
                    '62' => ['status' => 'cavity', 'notes' => 'Caries por biberón', 'updatedAt' => now()->subDays(20)->toISOString()],
                    '63' => ['status' => 'healthy', 'notes' => '', 'updatedAt' => now()->subDays(30)->toISOString()],
                    '64' => ['status' => 'healthy', 'notes' => '', 'updatedAt' => now()->subDays(30)->toISOString()],
                    '65' => ['status' => 'treated', 'notes' => 'Corona de acero', 'updatedAt' => now()->subDays(60)->toISOString()],
                    '71' => ['status' => 'missing', 'notes' => 'Exfoliación natural', 'updatedAt' => now()->subDays(90)->toISOString()],
                    '81' => ['status' => 'missing', 'notes' => 'Exfoliación natural', 'updatedAt' => now()->subDays(85)->toISOString()],
                ],
                'metadata' => [
                    'created_at' => now()->subDays(180)->toISOString(),
                    'last_updated' => now()->subDays(15)->toISOString(),
                    'version' => '1.0'
                ]
            ],

            // Cliente 4: Caso mixto (dentición permanente y temporal)
            [
                'permanent' => [
                    '11' => ['status' => 'healthy', 'notes' => 'Erupción reciente', 'updatedAt' => now()->subDays(20)->toISOString()],
                    '21' => ['status' => 'healthy', 'notes' => 'Erupción reciente', 'updatedAt' => now()->subDays(18)->toISOString()],
                    '16' => ['status' => 'healthy', 'notes' => 'Primer molar permanente', 'updatedAt' => now()->subDays(60)->toISOString()],
                    '26' => ['status' => 'cavity', 'notes' => 'Caries oclusal', 'updatedAt' => now()->subDays(10)->toISOString()],
                    '31' => ['status' => 'healthy', 'notes' => 'Erupción reciente', 'updatedAt' => now()->subDays(25)->toISOString()],
                    '41' => ['status' => 'healthy', 'notes' => 'Erupción reciente', 'updatedAt' => now()->subDays(22)->toISOString()],
                    '36' => ['status' => 'treated', 'notes' => 'Sellante de fosas y fisuras', 'updatedAt' => now()->subDays(90)->toISOString()],
                    '46' => ['status' => 'treated', 'notes' => 'Sellante de fosas y fisuras', 'updatedAt' => now()->subDays(90)->toISOString()],
                ],
                'temporary' => [
                    '52' => ['status' => 'healthy', 'notes' => '', 'updatedAt' => now()->subDays(30)->toISOString()],
                    '53' => ['status' => 'healthy', 'notes' => '', 'updatedAt' => now()->subDays(30)->toISOString()],
                    '54' => ['status' => 'cavity', 'notes' => 'Caries interproximal', 'updatedAt' => now()->subDays(12)->toISOString()],
                    '55' => ['status' => 'healthy', 'notes' => '', 'updatedAt' => now()->subDays(30)->toISOString()],
                    '62' => ['status' => 'healthy', 'notes' => '', 'updatedAt' => now()->subDays(30)->toISOString()],
                    '63' => ['status' => 'treated', 'notes' => 'Pulpotomía', 'updatedAt' => now()->subDays(45)->toISOString()],
                    '64' => ['status' => 'healthy', 'notes' => '', 'updatedAt' => now()->subDays(30)->toISOString()],
                    '65' => ['status' => 'healthy', 'notes' => '', 'updatedAt' => now()->subDays(30)->toISOString()],
                    '72' => ['status' => 'healthy', 'notes' => '', 'updatedAt' => now()->subDays(30)->toISOString()],
                    '73' => ['status' => 'healthy', 'notes' => '', 'updatedAt' => now()->subDays(30)->toISOString()],
                    '74' => ['status' => 'treated', 'notes' => 'Corona preformada', 'updatedAt' => now()->subDays(60)->toISOString()],
                    '75' => ['status' => 'healthy', 'notes' => '', 'updatedAt' => now()->subDays(30)->toISOString()],
                    '82' => ['status' => 'healthy', 'notes' => '', 'updatedAt' => now()->subDays(30)->toISOString()],
                    '83' => ['status' => 'healthy', 'notes' => '', 'updatedAt' => now()->subDays(30)->toISOString()],
                    '84' => ['status' => 'cavity', 'notes' => 'Caries inicial', 'updatedAt' => now()->subDays(8)->toISOString()],
                    '85' => ['status' => 'healthy', 'notes' => '', 'updatedAt' => now()->subDays(30)->toISOString()],
                ],
                'metadata' => [
                    'created_at' => now()->subDays(200)->toISOString(),
                    'last_updated' => now()->subDays(8)->toISOString(),
                    'version' => '1.0'
                ]
            ],

            // Cliente 5: Odontograma vacío (paciente nuevo)
            [
                'permanent' => [],
                'temporary' => [],
                'metadata' => [
                    'created_at' => now()->toISOString(),
                    'last_updated' => now()->toISOString(),
                    'version' => '1.0'
                ]
            ]
        ];

        foreach ($clients as $index => $client) {
            if (isset($odontogramExamples[$index])) {
                $client->update([
                    'odontogram' => $odontogramExamples[$index],
                    'dental_notes' => $this->generateDentalNotes($index),
                    'last_dental_visit' => now()->subDays(rand(10, 180))->toDateString(),
                ]);

                $this->command->info("✅ Odontograma poblado para: {$client->name}");
            }
        }

        $this->command->info("🦷 Seeding completado. {$clients->count()} odontogramas poblados.");
    }

    /**
     * Generar notas dentales de ejemplo
     */
    private function generateDentalNotes(int $index): string
    {
        $notes = [
            'Paciente con buena higiene oral. Requiere seguimiento de caries en desarrollo. Se recomienda uso de flúor tópico.',
            'Caso complejo con múltiples tratamientos. Paciente colaborador. Plan de tratamiento a largo plazo establecido.',
            'Paciente pediátrico. Padres educados sobre higiene oral. Control cada 6 meses recomendado.',
            'Dentición mixta en desarrollo normal. Vigilar erupción de permanentes. Posible necesidad de ortodoncia preventiva.',
            'Paciente nuevo. Evaluación inicial completada. Plan de tratamiento por establecer en próxima cita.'
        ];

        return $notes[$index] ?? 'Notas dentales de seguimiento.';
    }
}
