<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');                                                           // Nome do cliente
            $table->enum('type', ['empresa', 'pessoa_fisica'])->default('empresa');          // Tipo do cliente
            $table->string('email')->nullable();                                              // Email de contato
            $table->string('phone')->nullable();                                              // Telefone
            $table->string('document')->nullable();                                           // CNPJ ou CPF
            $table->text('notes')->nullable();                                                // Observações gerais
            $table->enum('segment', [                                                         // Segmento de viagem
                'corporativo',
                'lazer',
                'grupos',
                'lua_de_mel',
                'aventura',
                'cruzeiros',
            ])->default('corporativo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};