<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Team;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Cria 3 equipes
        $teams = Team::factory(3)->create();

        // Cria 10 gestores e 10 colaboradores
        $gestores     = User::factory(10)->create(['role' => 'gestor']);
        $colaboradores = User::factory(10)->create(['role' => 'colaborador']);

        // Distribui os usuários nas equipes
        foreach ($teams as $index => $team) {
            // Cada equipe recebe alguns gestores e colaboradores
            $team->users()->attach(
                $gestores->slice($index * 3, 3)->pluck('id'), // 3 gestores por equipe
                ['role_in_team' => 'gestor']
            );
            $team->users()->attach(
                $colaboradores->slice($index * 3, 3)->pluck('id'), // 3 colaboradores por equipe
                ['role_in_team' => 'colaborador']
            );
        }
    }
}