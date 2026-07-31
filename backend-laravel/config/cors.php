<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Con apiPrefix vacío (ver bootstrap/app.php) las rutas de routes/api.php
    | quedan registradas en la raíz (p. ej. "/login", no "/api/login"), así
    | que "paths" cubre todo en vez del típico "api/*" — el home ("/") y
    | "/media/*" de routes/web.php no se ven afectados por llevar también
    | cabeceras CORS.
    |
    */

    'paths' => ['*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
