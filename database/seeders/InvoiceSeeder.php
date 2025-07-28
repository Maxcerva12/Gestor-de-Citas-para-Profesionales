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
        // Buscar o crear un usuario
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Admin Usuario',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        // Buscar o crear un cliente
        $client = Client::first();
        if (!$client) {
            $client = Client::create([
                'name' => 'Juan Pérez García',
                'email' => 'juan.perez@ejemplo.com',
                'phone' => '+57 (1) 234-5678',
                'document_number' => '12.345.678-9',
                'document_type' => 'CC',
                'address' => 'Calle 123 #45-67, Barrio Centro',
                'city' => 'Bogotá',
                'state' => 'Cundinamarca',
                'postal_code' => '110111',
                'company' => 'Servicios Empresariales ABC S.A.S.',
            ]);
        }

        // Crear factura de ejemplo
        $invoice = Invoice::create([
            'type' => InvoiceType::Invoice,
            'state' => InvoiceState::Draft,
            'description' => 'Servicios de consultoría y desarrollo web para el mes de julio 2025',
            'due_at' => now()->addDays(30),
            'user_id' => $user->id,
            'buyer_id' => $client->id,
            'seller_information' => config('invoices.default_seller'),
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
                    'Régimen Fiscal' => 'Común',
                ],
            ],
        ]);

        // Configurar número de serie
        $invoice->configureSerialNumber(
            prefix: 'FAC',
            year: now()->format('Y'),
            month: now()->format('m')
        );
        $invoice->save();

        // Crear items de factura
        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'label' => 'Desarrollo de Aplicación Web',
            'description' => 'Desarrollo completo de aplicación web con Laravel y Vue.js, incluyendo panel administrativo',
            'unit_price' => Money::of(2500000, 'COP'), // $2,500,000 COP
            'quantity' => 1,
            'tax_percentage' => 19,
            'order' => 1,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'label' => 'Hosting y Dominio',
            'description' => 'Servicio de hosting anual con dominio .com.co incluido',
            'unit_price' => Money::of(350000, 'COP'), // $350,000 COP
            'quantity' => 1,
            'tax_percentage' => 19,
            'order' => 2,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'label' => 'Mantenimiento Mensual',
            'description' => 'Servicio de mantenimiento y soporte técnico mensual',
            'unit_price' => Money::of(180000, 'COP'), // $180,000 COP
            'quantity' => 3,
            'tax_percentage' => 19,
            'order' => 3,
        ]);

        // Crear una segunda factura pagada
        $paidInvoice = Invoice::create([
            'type' => InvoiceType::Invoice,
            'state' => InvoiceState::Paid,
            'description' => 'Consultoría en sistemas de información empresarial',
            'due_at' => now()->subDays(15),
            'paid_at' => now()->subDays(5),
            'user_id' => $user->id,
            'buyer_id' => $client->id,
            'seller_information' => config('invoices.default_seller'),
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
        ]);

        $paidInvoice->configureSerialNumber(
            prefix: 'FAC',
            year: now()->format('Y'),
            month: now()->format('m')
        );
        $paidInvoice->save();

        InvoiceItem::create([
            'invoice_id' => $paidInvoice->id,
            'label' => 'Consultoría Empresarial',
            'description' => 'Análisis y optimización de procesos empresariales',
            'unit_price' => Money::of(1200000, 'COP'),
            'quantity' => 1,
            'tax_percentage' => 19,
            'order' => 1,
        ]);

        $this->command->info('Facturas de prueba creadas exitosamente');
    }
}
