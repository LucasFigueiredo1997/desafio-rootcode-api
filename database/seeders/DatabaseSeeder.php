<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // O DatabaseSeeder orquestra quais seeders rodar e em qual ordem
        $this->call([
            UserSeeder::class,   // Cria equipes, gestores e colaboradores
            ClientSeeder::class, // Cria os clientes mockados de agência de viagem
        ]);
    }
}