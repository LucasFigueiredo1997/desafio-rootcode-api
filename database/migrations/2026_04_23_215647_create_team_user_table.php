<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabela auxiliar que liga usuários às equipes (relacionamento muitos para muitos)
        Schema::create('team_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('restrict');   // ID da equipe
            $table->foreignId('user_id')->constrained()->onDelete('restrict');   // ID do usuário
            $table->enum('role_in_team', ['gestor', 'colaborador']);             // Papel do usuário dentro da equipe
            $table->timestamps();

            // Garante que o mesmo usuário não seja adicionado duas vezes na mesma equipe
            $table->unique(['team_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_user');
    }
};