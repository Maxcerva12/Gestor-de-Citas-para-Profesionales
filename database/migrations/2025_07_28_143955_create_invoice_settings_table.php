<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoice_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->string('type')->default('string'); // string, json, file, color, etc.
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insertar configuraciones por defecto
        $this->insertDefaultSettings();
    }

    /**
     * Insertar configuraciones por defecto
     */
    private function insertDefaultSettings()
    {
        $defaultSettings = [
            [
                'key' => 'company_info',
                'value' => json_encode([
                            'company' => 'Mi Empresa Colombia',
                            'name' => null,
                            'address' => [
                                    'street' => 'Carrera 7 #32-16',
                                    'city' => 'Bogotá',
                                    'postal_code' => '110311',
                                    'state' => 'Cundinamarca',
                                    'country' => 'Colombia',
                                ],
                            'email' => 'contacto@miempresa.com.co',
                            'phone' => '+57 (1) 234-5678',
                            'tax_number' => '900.123.456-7',
                            'fields' => [
                                    'Régimen' => 'Común',
                                    'Actividad Económica' => 'Servicios Profesionales',
                                ],
                        ]),
                'type' => 'json',
                'description' => 'Información de la empresa emisora',
            ],
            [
                'key' => 'pdf_template_color',
                'value' => json_encode('#1e40af'),
                'type' => 'color',
                'description' => 'Color principal de la plantilla PDF',
            ],
            [
                'key' => 'pdf_font',
                'value' => json_encode('Helvetica'),
                'type' => 'select',
                'description' => 'Fuente tipográfica para PDFs',
            ],
            [
                'key' => 'company_logo',
                'value' => null,
                'type' => 'file',
                'description' => 'Logo de la empresa para facturas',
            ],
            [
                'key' => 'invoice_template',
                'value' => json_encode('colombia.layout'),
                'type' => 'select',
                'description' => 'Plantilla de factura a utilizar',
            ],
            [
                'key' => 'tax_rate',
                'value' => json_encode(19),
                'type' => 'number',
                'description' => 'Tasa de IVA por defecto (%)',
            ],
        ];

        foreach ($defaultSettings as $setting) {
            DB::table('invoice_settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_settings');
    }
};
