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
        Schema::create('items', function (Blueprint $table) {
        $table->id();
        $table->string('nombre'); // Ej: Taladro Makita, Cámara Sony
        $table->string('categoria'); // Ej: Herramientas, Electrónica
        // Usamos ENUM para restringir los estados posibles
        $table->enum('estado', ['disponible', 'prestado', 'mantenimiento', 'atrasado'])->default('disponible');
        $table->integer('vida_util_max'); // Horas totales de uso antes de revisión
        $table->integer('uso_acumulado')->default(0); // Horas que ya se ha usado
        $table->timestamps(); // Crea 'created_at' y 'updated_at' automáticos
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
