<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');   // Se a tarefa for deletada, os comentários também são
            $table->foreignId('user_id')->constrained()->onDelete('cascade');   // Se o usuário for deletado, seus comentários também são
            $table->text('content');                                             // Conteúdo do comentário
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_comments');
    }
};