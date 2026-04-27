<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            ['name' => 'Petrobras S.A.', 'type' => 'empresa', 'email' => 'viagens@petrobras.com.br', 'phone' => '(21) 3224-4477', 'document' => '33.000.167/0001-01', 'segment' => 'corporativo', 'notes' => 'Cliente premium. Viagens executivas mensais.'],
            ['name' => 'Embraer S.A.', 'type' => 'empresa', 'email' => 'travel@embraer.com.br', 'phone' => '(12) 3927-1000', 'document' => '07.689.002/0001-89', 'segment' => 'corporativo', 'notes' => 'Viagens técnicas frequentes para Europa e EUA.'],
            ['name' => 'Ricardo e Fernanda Oliveira', 'type' => 'pessoa_fisica', 'email' => 'ricardo.fernanda@gmail.com', 'phone' => '(11) 99234-5678', 'document' => '123.456.789-00', 'segment' => 'lua_de_mel', 'notes' => 'Lua de mel para Maldivas. Março 2027.'],
            ['name' => 'Clube de Aventureiros SP', 'type' => 'empresa', 'email' => 'contato@clubeaventura.com.br', 'phone' => '(11) 3456-7890', 'document' => '12.345.678/0001-90', 'segment' => 'aventura', 'notes' => 'Grupos de 20-40 pessoas. Destinos de trekking e ecoturismo.'],
            ['name' => 'MSC Cruzeiros Brasil', 'type' => 'empresa', 'email' => 'grupos@msccruzeiros.com.br', 'phone' => '(21) 2121-3030', 'document' => '45.678.901/0001-23', 'segment' => 'cruzeiros', 'notes' => 'Pacotes de cruzeiros pelo Mediterrâneo e Caribe.'],
            ['name' => 'Família Souza', 'type' => 'pessoa_fisica', 'email' => 'familia.souza@hotmail.com', 'phone' => '(31) 98765-4321', 'document' => '987.654.321-00', 'segment' => 'lazer', 'notes' => 'Viagem de férias em família. Prefere resorts all-inclusive.'],
            ['name' => 'Grupo Escolar Colégio Elite', 'type' => 'empresa', 'email' => 'excursoes@colegio-elite.com.br', 'phone' => '(11) 4567-8901', 'document' => '23.456.789/0001-01', 'segment' => 'grupos', 'notes' => 'Excursões educacionais anuais para Europa. Grupos de 50+ alunos.'],
            ['name' => 'Vale S.A.', 'type' => 'empresa', 'email' => 'corporate.travel@vale.com', 'phone' => '(21) 3814-4477', 'document' => '33.592.510/0001-54', 'segment' => 'corporativo', 'notes' => 'Viagens para operações em Carajás, Moçambique e Canadá.'],
            ['name' => 'Ana Carolina Mendes', 'type' => 'pessoa_fisica', 'email' => 'anacarolina.viagens@gmail.com', 'phone' => '(85) 99876-5432', 'document' => '456.789.123-00', 'segment' => 'aventura', 'notes' => 'Viajante solo. Interesse em trilhas e mergulho.'],
            ['name' => 'Banco Itaú S.A.', 'type' => 'empresa', 'email' => 'viagens.corporativas@itau.com.br', 'phone' => '(11) 5060-7000', 'document' => '60.701.190/0001-04', 'segment' => 'corporativo', 'notes' => 'Viagens executivas para toda a América Latina.'],
        ];

        foreach ($clients as $client) {
            Client::create($client);
        }
    }
}