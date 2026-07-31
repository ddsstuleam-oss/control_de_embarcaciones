<?php

use Knuckles\Scribe\Config\AuthIn;
use Knuckles\Scribe\Config\Defaults;
use Knuckles\Scribe\Extracting\Strategies;

use function Knuckles\Scribe\Config\configureStrategy;
use function Knuckles\Scribe\Config\removeStrategies;

return [

    'title' => 'API — Control de Embarcaciones FCVT ULEAM',

    'description' => 'Documentación oficial de la API REST del sistema institucional de control y reservas de embarcaciones de la Facultad de Ciencias de la Vida y Tecnologías — Universidad Laica Eloy Alfaro de Manabí.',

    'intro_text' => <<<'INTRO'
Esta documentación describe todos los endpoints disponibles en el sistema de control de embarcaciones FCVT-ULEAM.

## Autenticación
El sistema usa **Laravel Sanctum** con tokens Bearer. Para autenticarte:
1. Haz `POST /api/login` con tu cédula y contraseña
2. Copia el `token` de la respuesta
3. Úsalo en el header: `Authorization: Bearer {token}`

## Roles disponibles
- **admin** — acceso total al sistema
- **operador** — validación de boletos en puerto
- **usuario** — reservas y boletos propios

<aside>Los ejemplos de código están disponibles en bash, javascript y php en el panel derecho.</aside>
INTRO,

    'base_url' => config('app.url'),

    'routes' => [
        [
            'match' => [
                'prefixes' => ['api/*'],
                'domains'  => ['*'],
            ],
            'include' => [],
            'exclude' => [],
        ],
    ],

    'type'  => 'laravel',
    'theme' => 'default',

    'static' => [
        'output_path' => 'public/docs',
    ],

    'laravel' => [
        'add_routes'        => true,
        'docs_url'          => '/docs',
        'assets_directory'  => null,
        'middleware'        => [],
    ],

    'external' => [
        'html_attributes' => [],
    ],

    'try_it_out' => [
        'enabled'  => true,
        'base_url' => null,
        'use_csrf' => false,
        'csrf_url' => '/sanctum/csrf-cookie',
    ],

    'auth' => [
        'enabled'     => true,
        'default'     => true,
        'in'          => AuthIn::BEARER->value,
        'name'        => 'Authorization',
        'use_value'   => env('SCRIBE_AUTH_KEY'),
        'placeholder' => '{TU_TOKEN_AQUI}',
        'extra_info'  => 'Obtén el token haciendo <b>POST /api/login</b> con tu cédula y contraseña. El token tiene una sola sesión activa a la vez.',
    ],

    'example_languages' => [
        'bash',
        'javascript',
        'php',
    ],

    'postman' => [
        'enabled'   => true,
        'overrides' => [],
    ],

    'openapi' => [
        'enabled'    => true,
        'version'    => '3.0.3',
        'overrides'  => [],
        'generators' => [],
    ],

    'groups' => [
        'default' => 'Endpoints',
        'order'   => [
            'Autenticación',
            'Recuperación de contraseña',
            'Perfil',
            'Embarcaciones',
            'Reservas',
            'Boletos',
            'Actividad',
            'Admin — Dashboard',
            'Admin — Usuarios',
            'Admin — Reportes',
        ],
    ],

    'logo' => 'images/logo-uleam-nuevo.png',

    'last_updated' => 'Actualizado: {date:d/m/Y}',

    'examples' => [
        'faker_seed'    => 1234,
        'models_source' => ['factoryCreate', 'factoryMake', 'databaseFirst'],
    ],

    'strategies' => [
        'metadata' => [
            ...Defaults::METADATA_STRATEGIES,
        ],
        'headers' => [
            ...Defaults::HEADERS_STRATEGIES,
            Strategies\StaticData::withSettings(data: [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ]),
        ],
        'urlParameters' => [
            ...Defaults::URL_PARAMETERS_STRATEGIES,
        ],
        'queryParameters' => [
            ...Defaults::QUERY_PARAMETERS_STRATEGIES,
        ],
        'bodyParameters' => [
            ...Defaults::BODY_PARAMETERS_STRATEGIES,
        ],
        'responses' => configureStrategy(
            Defaults::RESPONSES_STRATEGIES,
            Strategies\Responses\ResponseCalls::withSettings(
                only: ['GET *'],
                config: [
                    'app.debug' => false,
                ]
            )
        ),
        'responseFields' => [
            ...Defaults::RESPONSE_FIELDS_STRATEGIES,
        ],
    ],

    'database_connections_to_transact' => [config('database.default')],

    'fractal' => [
        'serializer' => null,
    ],
];