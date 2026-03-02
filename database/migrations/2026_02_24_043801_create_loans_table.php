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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            // Estas relaciones se mantienen igual, son seguras
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // CORRECCIÓN: Añadimos ->nullable() para evitar el error 1067 de MySQL
            $table->timestamp('fecha_prestamo')->nullable();
            $table->timestamp('fecha_devolucion_esperada')->nullable();
            
            // Este ya estaba bien, pero lo mantenemos por consistencia
            $table->timestamp('fecha_devolucion_real')->nullable(); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};