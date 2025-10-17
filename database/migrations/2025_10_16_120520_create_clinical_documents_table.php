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
        Schema::create('clinical_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_history_id')->constrained('medical_histories')->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->onDelete('set null');

            $table->string('tipo_documento');
            $table->string('nombre_documento');
            $table->text('descripcion')->nullable();

            // Información del archivo
            $table->string('archivo_path');
            $table->string('archivo_nombre_original');
            $table->string('archivo_mime_type');
            $table->bigInteger('archivo_size')->unsigned();

            $table->date('fecha_documento');
            $table->foreignId('subido_por')->nullable()->constrained('users')->onDelete('set null');
            $table->text('observaciones')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinical_documents');
    }
};
