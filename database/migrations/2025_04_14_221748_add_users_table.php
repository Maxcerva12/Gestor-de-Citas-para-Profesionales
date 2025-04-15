<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('document_type')->nullable(); // Documento de identidad (opcional)
            $table->string('document_number')->nullable(); // Número de documento (opcional)
            $table->string('phone')->nullable(); // Teléfono (opcional)
            $table->string('address')->nullable(); // Dirección (opcional)
            $table->string('city')->nullable(); // Ciudad (opcional)
            $table->string('country')->nullable(); // País (opcional)
            $table->string('profession')->nullable(); // Profesión (opcional)
            $table->string('especialty')->nullable(); // Especialidad (opcional)
            $table->string('description')->nullable(); // Descripción (opcional)
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            
        });
    }
};
