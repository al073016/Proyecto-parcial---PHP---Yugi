<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Item; // Importante: añade esta línea si no está
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Esto creará tus 50 objetos aleatorios
        Item::factory(50)->create();

        // Esto crea un usuario de prueba para que tú y tu compañero puedan loguearse
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'), // Añade esto para que sepan la clave
        ]);
    }
}