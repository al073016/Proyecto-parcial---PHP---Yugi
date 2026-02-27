<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Item;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Creamos 50 objetos aleatorios de prueba
        Item::factory(50)->create();

        // Usuario Administrador (Bibliotecario)
        User::factory()->create([
            'name'       => 'Admin Bibliotecario',
            'email'      => 'admin@example.com',
            'password'   => bcrypt('password'),
            'rol'        => 'admin',
            'bloqueado'  => false,
            'reputacion' => 100,
        ]);

        // Usuario Alumno de prueba
        User::factory()->create([
            'name'       => 'Test Alumno',
            'email'      => 'test@example.com',
            'password'   => bcrypt('password'),
            'rol'        => 'alumno',
            'bloqueado'  => false,
            'reputacion' => 100,
        ]);
    }
}
