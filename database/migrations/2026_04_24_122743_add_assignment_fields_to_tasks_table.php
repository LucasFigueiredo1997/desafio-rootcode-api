<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // ID do colaborador responsável pela tarefa
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null')->after('user_id');

            // ID do time responsável pela tarefa
            $table->foreignId('team_id')->nullable()->constrained('teams')->onDelete('set null')->after('assigned_to');

            // Link para documentação externa (ex: SharePoint, Confluence, Notion) — opcional
            $table->string('documentation_url')->nullable()->after('team_id');

            // Responsável pela revisão da tarefa — opcional
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->onDelete('set null')->after('documentation_url');

            // Adiciona "em revisao" e "em andamento" ao enum de status
            \DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('pendente', 'em_andamento', 'em_revisao', 'concluido') DEFAULT 'pendente'");
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropForeign(['team_id']);
            $table->dropForeign(['reviewer_id']);
            $table->dropColumn(['assigned_to', 'team_id', 'documentation_url', 'reviewer_id']);

            // Reverte o enum para o estado original
            \DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('pendente', 'em_andamento', 'em_revisao', 'concluido') DEFAULT 'pendente'");
        });
    }
};