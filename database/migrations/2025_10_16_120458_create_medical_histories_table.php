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
        Schema::create('medical_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');

            // Datos Generales de Salud
            $table->text('motivo_consulta')->nullable();
            $table->text('enfermedad_actual')->nullable();
            $table->text('antecedentes_personales')->nullable();
            $table->text('antecedentes_familiares')->nullable();
            $table->text('habitos')->nullable();
            $table->text('medicamentos_actuales')->nullable();
            $table->text('alergias_medicamentos')->nullable();
            $table->text('cirugias_previas')->nullable();
            $table->text('hospitalizaciones')->nullable();
            $table->text('transfusiones')->nullable();
            $table->text('enfermedades_cronicas')->nullable();

            // Antecedentes Odontológicos
            $table->date('ultima_visita_odontologo')->nullable();
            $table->text('motivo_ultima_visita')->nullable();
            $table->text('tratamientos_previos')->nullable();
            $table->text('experiencias_traumaticas')->nullable();
            $table->string('higiene_oral_frecuencia')->nullable();
            $table->boolean('sangrado_encias')->default(false);
            $table->boolean('sensibilidad_dental')->default(false);
            $table->boolean('bruxismo')->default(false);
            $table->boolean('ortodoncia_previa')->default(false);

            // Odontograma
            $table->json('odontogram')->nullable();
            $table->text('odontogram_observations')->nullable();
            $table->timestamp('odontogram_last_update')->nullable();

            // Información Adicional
            $table->text('observaciones_generales')->nullable();
            $table->text('plan_tratamiento')->nullable();
            $table->text('diagnostico_principal')->nullable();
            $table->text('pronostico')->nullable();
            $table->boolean('consentimiento_informado')->default(false);

            // Metadatos
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_histories');
    }
};
