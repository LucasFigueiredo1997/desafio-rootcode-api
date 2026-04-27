<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');   // Se a tarefa for deletada, os anexos também são
            $table->foreignId('user_id')->constrained()->onDelete('cascade');   // Quem fez o upload
            $table->string('filename');                                          // Nome original do arquivo
            $table->string('path');                                              // Caminho onde o arquivo está salvo
            $table->string('mime_type')->nullable();                             // Tipo do arquivo (pdf, png, etc.)
            $table->unsignedBigInteger('size')->nullable();                      // Tamanho em bytes
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_attachments');
    }
};