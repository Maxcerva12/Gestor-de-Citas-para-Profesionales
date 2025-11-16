<?php

namespace Database\Seeders;

use App\Enums\InvoiceState;
use App\Enums\InvoiceType;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use Brick\Money\Money;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creando facturas de ejemplo para gráfica de ingresos...');

        // Buscar o crear usuarios
        $users = User::limit(3)->get();
        if ($users->count() < 3) {
            $users = collect();
            for ($i = 0; $i < 3; $i++) {
                $users->push(User::firstOrCreate([
                    'email' => "usuario{$i}@ejemplo.com"
                ], [
                    'name' => "Usuario {$i}",
                    'password' => bcrypt('password'),
                ]));
            }
        }

        // Buscar o crear clientes
        $clients = Client::limit(5)->get();
        if ($clients->count() < 5) {
            $clients = collect();
            $clientsData = [
                ['Juan Pérez García', 'juan.perez@ejemplo.com', '12.345.678-9', 'Servicios Empresariales ABC S.A.S.'],
                ['María López Rodríguez', 'maria.lopez@empresa.com', '23.456.789-0', 'Consultoría XYZ Ltda.'],
                ['Carlos Martínez Silva', 'carlos.martinez@negocio.co', '34.567.890-1', 'Inversiones DEF S.A.'],
                ['Ana García Morales', 'ana.garcia@corporacion.com', '45.678.901-2', 'Corporación GHI'],
                ['Luis Rodríguez Torres', 'luis.rodriguez@grupo.co', '56.789.012-3', 'Grupo JKL S.A.S.']
            ];

            foreach ($clientsData as $index => $clientData) {
                $clients->push(Client::firstOrCreate([
                    'email' => $clientData[1]
                ], [
                    'name' => $clientData[0],
                    'phone' => '+57 (1) ' . rand(200, 999) . '-' . rand(1000, 9999),
                    'document_number' => $clientData[2],
                    'document_type' => 'CC',
                    'address' => 'Calle ' . rand(10, 200) . ' #' . rand(10, 99) . '-' . rand(10, 99),
                    'city' => ['Bogotá', 'Medellín', 'Cali', 'Barranquilla', 'Cartagena'][array_rand(['Bogotá', 'Medellín', 'Cali', 'Barranquilla', 'Cartagena'])],
                    'state' => 'Colombia',
                    'postal_code' => '1101' . rand(10, 99),
                    'company' => $clientData[3],
                ]));
            }
        }

        // Generar facturas para los últimos 12 meses
        $this->generateInvoicesForPeriod($users, $clients, 12);

        $this->command->info('✅ Facturas de ejemplo creadas exitosamente para el dashboard de ingresos');
    }

    /**
     * Generar facturas distribuidas a lo largo de un período
     */
    private function generateInvoicesForPeriod($users, $clients, int $months): void
    {
        $startDate = now()->subMonths($months);
        $endDate = now();
        $invoiceCounter = 1; // Contador global para números de serie únicos

        // Servicios de ejemplo con precios realistas
        $services = [
            ['Desarrollo Web Completo', 'Desarrollo de sitio web responsive con CMS', 2500000, 3500000],
            ['Aplicación Móvil', 'Desarrollo de app nativa iOS y Android', 4000000, 6000000],
            ['Consultoría IT', 'Análisis y consultoría en tecnología', 800000, 1500000],
            ['Diseño Gráfico', 'Identidad corporativa y material publicitario', 500000, 1200000],
            ['Marketing Digital', 'Estrategia de marketing y redes sociales', 600000, 1800000],
            ['Hosting y Dominio', 'Servicios de hosting anual empresarial', 200000, 500000],
            ['Mantenimiento Web', 'Mantenimiento mensual de sitio web', 150000, 400000],
            ['E-commerce', 'Tienda online con pasarela de pagos', 3000000, 5000000],
            ['Sistema CRM', 'Sistema de gestión de clientes personalizado', 2000000, 4000000],
            ['Capacitación Digital', 'Talleres de transformación digital', 400000, 800000],
        ];

        // Generar facturas distribuidas
        for ($month = 0; $month < $months; $month++) {
            $monthDate = $startDate->copy()->addMonths($month);
            
            // Generar entre 5 y 15 facturas por mes
            $invoicesPerMonth = rand(5, 15);
            
            for ($i = 0; $i < $invoicesPerMonth; $i++) {
                $user = $users->random();
                $client = $clients->random();
                $service = $services[array_rand($services)];
                
                // Fecha aleatoria dentro del mes
                $invoiceDate = $monthDate->copy()->addDays(rand(1, 28));
                
                // 85% probabilidad de estar pagada
                $isPaid = rand(1, 100) <= 85;
                $state = $isPaid ? InvoiceState::Paid : (rand(1, 2) == 1 ? InvoiceState::Draft : InvoiceState::Sent);
                
                $invoice = Invoice::create([
                    'type' => InvoiceType::Invoice,
                    'state' => $state,
                    'description' => $service[1] . ' - ' . $monthDate->format('F Y'),
                    'due_at' => $invoiceDate->copy()->addDays(30),
                    'paid_at' => $isPaid ? $invoiceDate->copy()->addDays(rand(1, 25)) : null,
                    'user_id' => $user->id,
                    'buyer_id' => $client->id,
                    'created_at' => $invoiceDate,
                    'updated_at' => $invoiceDate,
                    'seller_information' => config('invoices.default_seller', [
                        'company' => 'Mi Empresa S.A.S.',
                        'name' => 'Empresa de Servicios',
                        'email' => 'facturacion@miempresa.com',
                        'phone' => '+57 (1) 234-5678',
                        'address' => [
                            'street' => 'Calle 100 #20-30',
                            'city' => 'Bogotá',
                            'postal_code' => '110111',
                            'state' => 'Cundinamarca',
                            'country' => 'Colombia',
                        ]
                    ]),
                    'buyer_information' => [
                        'company' => $client->company,
                        'name' => $client->name,
                        'email' => $client->email,
                        'phone' => $client->phone,
                        'address' => [
                            'street' => $client->address,
                            'city' => $client->city,
                            'postal_code' => $client->postal_code,
                            'state' => $client->state,
                            'country' => 'Colombia',
                        ],
                        'fields' => [
                            'Documento' => $client->document_number,
                            'Tipo de Cliente' => 'Jurídica',
                        ],
                    ],
                    // Generar número de serie único manualmente
                    'serial_number_format' => 'PPYYCCCC',
                    'serial_number_prefix' => 'FAC',
                    'serial_number_year' => $invoiceDate->format('y'),
                    'serial_number_month' => null,
                    'serial_number_serie' => null,
                    'serial_number_count' => $invoiceCounter,
                    'serial_number' => 'FAC' . $invoiceDate->format('y') . str_pad($invoiceCounter, 4, '0', STR_PAD_LEFT),
                ]);

                $invoiceCounter++; // Incrementar contador para siguiente factura

                // Crear items de la factura
                $itemCount = rand(1, 3); // Entre 1 y 3 items por factura
                
                for ($j = 0; $j < $itemCount; $j++) {
                    $itemService = $services[array_rand($services)];
                    $basePrice = rand($itemService[2], $itemService[3]);
                    $quantity = rand(1, 3);
                    
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'label' => $itemService[0],
                        'description' => $itemService[1],
                        'unit_price' => Money::of($basePrice, 'COP'),
                        'quantity' => $quantity,
                        'tax_percentage' => 19,
                        'order' => $j + 1,
                    ]);
                }
            }
            
            $this->command->info("✓ Mes {$monthDate->format('M Y')}: {$invoicesPerMonth} facturas creadas");
        }
    }
}
