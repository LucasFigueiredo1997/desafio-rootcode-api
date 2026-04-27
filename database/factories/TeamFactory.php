<?php

namespace Database\Factories;

use App\Models\Team; // Importa o Model Team que vamos criar logo depois
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Team>
 */
class TeamFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Equipe', 'Time', 'Squad']) . ' ' . fake()->word(), // Gera nomes como "Equipe Alpha", "Time Bravo", "Squad Delta"
        ];
    }
}