<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Rol: 'admin' = Bibliotecario/Administrador, 'alumno' = Usuario normal
            $table->enum('rol', ['admin', 'alumno'])->default('alumno')->after('email');
            // Si el alumno tiene un préstamo atrasado, se bloquea
            $table->boolean('bloqueado')->default(false)->after('rol');
            // Reputación del alumno (score), empieza en 100
            $table->integer('reputacion')->default(100)->after('bloqueado');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['rol', 'bloqueado', 'reputacion']);
        });
    }
};
