<?php

// Importações das ferramentas do Laravel que vamos usar
use Illuminate\Database\Migrations\Migration;  // Classe base para migrations
use Illuminate\Database\Schema\Blueprint;      // Define as colunas da tabela
use Illuminate\Support\Facades\Schema;         // Executa operações no banco

return new class extends Migration
{
    // Executado quando rodamos "php artisan migrate" — CRIA a tabela
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();                                                          // Coluna "id" — número único e automático (1, 2, 3...)
            $table->foreignId('user_id')->constrained()->onDelete('restrict');     // Liga a tarefa a um usuário. Se o usuário for deletado, suas tarefas também são
            $table->string('title');                                               // Coluna de texto curto para o título da tarefa
            $table->text('description')->nullable();                               // Texto longo para descrição — nullable() significa que é opcional
            $table->enum('status', ['pendente', 'concluido'])->default('pendente'); // Status só aceita "pendente" ou "concluido". Padrão: "pendente"
            $table->enum('difficulty', ['facil', 'medio', 'dificil'])->default('medio');           // Dificuldade da tarefa — facilita a distribuição de tasks pela gestão
            $table->date('due_date')->default(now()->addWeek());                    // Prazo limite da tarefa — padrão: 1 semana a partir da criação
            $table->timestamps();                                                  // Cria "created_at" e "updated_at" automaticamente
        });
    }

    // Executado quando rodamos "php artisan migrate:rollback" — APAGA a tabela
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};