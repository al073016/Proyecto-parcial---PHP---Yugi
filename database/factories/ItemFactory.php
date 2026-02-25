<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'nombre' => $this->faker->words(2, true), // Nombres como "Sierra Eléctrica"
        'categoria' => $this->faker->randomElement(['Herramientas', 'Tecnología', 'Audiovisual', 'Libros Técnicos']),
        'estado' => $this->faker->randomElement(['disponible', 'prestado', 'mantenimiento', 'atrasado']),
        'vida_util_max' => $this->faker->numberBetween(50, 200),
        'uso_acumulado' => $this->faker->numberBetween(0, 40),
    ];
    }
}
