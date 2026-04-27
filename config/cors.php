<?php

return [
    'paths'                    => ['api/*', 'sanctum/csrf-cookie'], // Rotas que aceitam requisições externas
    'allowed_methods'          => ['*'],                            // Aceita todos os métodos (GET, POST, PUT, DELETE)
    'allowed_origins'          => ['http://localhost:3000'],        // Aceita requisições do frontend Next.js
    'allowed_origins_patterns' => [],
    'allowed_headers'          => ['*'],                            // Aceita todos os headers
    'exposed_headers'          => [],
    'max_age'                  => 0,
    'supports_credentials'     => true,                             // Necessário para o Sanctum funcionar com cookies
];