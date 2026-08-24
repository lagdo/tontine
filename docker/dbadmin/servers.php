<?php

return [
    'common' => [
        'access' => [
            'server' => true,
            'system' => false,
        ],
    ],
    'fallback' => [
        'default' => '',
        'servers' => [
            // The database servers
            'tontine' => [
                'driver' => 'pgsql',
                'name' => 'Tontine',
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', '5432'),
                'username' => env('DB_USERNAME'),
                'password' => env('DB_PASSWORD'),
            ],
        ],
    ],
];
