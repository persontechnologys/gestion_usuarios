<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // Permite los orígenes de los frontends definidos en FRONTEND_URLS (lista separada por comas).
    'allowed_origins' => array_values(
        // Descarta los valores vacíos que dejan las comas sobrantes o repetidas.
        array_filter(
            // Elimina los espacios alrededor de cada origen.
            array_map(
                // Aplica la función nativa trim a cada elemento.
                'trim',
                // Separa la cadena de orígenes por comas, con valores por defecto para desarrollo local.
                explode(',', (string) env('FRONTEND_URLS', 'http://localhost:3000,http://localhost:5500,http://127.0.0.1:5500'))
            ),
            // Conserva únicamente las cadenas con contenido.
            fn (string $origen): bool => $origen !== ''
        )
    ),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
