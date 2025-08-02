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
        Schema::table('clients', function (Blueprint $table) {
            // Dividir el campo name existente
            $table->string('apellido')->nullable()->after('name');

            // Campos de documento
            $table->enum('tipo_documento', ['CC', 'CE', 'TI', 'PP'])
                ->default('CC')
                ->comment('CC=Cédula de Ciudadanía, CE=Cédula de Extranjería, TI=Tarjeta de Identidad, PP=Pasaporte')
                ->after('apellido');
            $table->string('numero_documento', 50)->nullable()->after('tipo_documento');

            // Información personal
            $table->enum('genero', ['Masculino', 'Femenino', 'Otro'])->nullable()->after('numero_documento');
            $table->date('fecha_nacimiento')->nullable()->after('genero');
            $table->enum('tipo_sangre', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable()->after('fecha_nacimiento');

            // Información médica
            $table->text('historial_medico')->nullable()->after('tipo_sangre');
            $table->text('alergias')->nullable()->after('historial_medico');

            // Aseguradora
            $table->string('aseguradora')->nullable()->after('alergias');

            // Contacto de emergencia
            $table->string('nombre_contacto_emergencia')->nullable()->after('aseguradora');
            $table->string('telefono_contacto_emergencia', 20)->nullable()->after('nombre_contacto_emergencia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'apellido',
                'tipo_documento',
                'numero_documento',
                'genero',
                'fecha_nacimiento',
                'tipo_sangre',
                'historial_medico',
                'alergias',
                'aseguradora',
                'nombre_contacto_emergencia',
                'telefono_contacto_emergencia',
            ]);
        });
    }
};
