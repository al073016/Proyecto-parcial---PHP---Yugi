<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            // Estado físico al salir y al regresar
            $table->enum('estado_fisico_salida', ['bueno', 'regular', 'malo'])->default('bueno')->after('fecha_devolucion_real');
            $table->enum('estado_fisico_entrada', ['bueno', 'regular', 'malo'])->nullable()->after('estado_fisico_salida');
            // Multa calculada si hay retraso o daño
            $table->decimal('monto_multa', 8, 2)->default(0)->after('estado_fisico_entrada');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['estado_fisico_salida', 'estado_fisico_entrada', 'monto_multa']);
        });
    }
};
