<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre del cliente
            $table->string('email')->unique(); // Email único para autenticación
            $table->string('password'); // Contraseña cifrada
            $table->string('phone')->nullable(); // Teléfono (opcional)
            $table->string('address')->nullable(); // Dirección (opcional)
            $table->string('city')->nullable(); // Ciudad (opcional)
            $table->string('country')->nullable(); // País (opcional)
            $table->timestamps(); // Campos created_at y updated_at
        });
        
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
