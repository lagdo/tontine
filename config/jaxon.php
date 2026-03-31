<?php

return [
    'app' => [
        'metadata' => [
            'format' => 'attributes',
            'cache' => [
                'enabled' => !env('APP_DEBUG', true),
                'dir' => storage_path('attributes'),
            ]
        ],
        'faker' => env('APP_FAKER', false),
        'helpers' => true,
        'request' => [
            'route' => 'jaxon.ajax',
            'middlewares' => [
                'web',
                'tenant',
                'tontine',
                'analytics',
                'jaxon.config',
                'jaxon.ajax',
            ],
        ],
        // The "directories" options are set by the TontineJaxon middleware.
        // 'directories' => [],
        'views' => [
            // The "pagination" options are set by the TontineTemplate middleware.
            // 'pagination' => [],
        ],
        'assets' => [
            'uri' => env('TONTINE_ASSETS_URI', '/jaxon'),
            'dir' => env('TONTINE_ASSETS_DIR', public_path('/jaxon')),
            'export' => env('TONTINE_ASSETS_EXPORT', !env('APP_DEBUG')),
            'minify' => env('TONTINE_ASSETS_MINIFY', !env('APP_DEBUG')),
            // The "file" option is set by the TontineJaxon middleware.
            // 'file' => env('TONTINE_ASSETS_FILE', 'tontine-4.0.9'),
        ],
        'packages' => [
        ],
        'dialogs' => [
            'default' => [
                'modal' => 'bootbox',
                'alert' => 'notyf',
                'confirm' => 'noty',
            ],
        ],
        'options' => [
            'logging' => [
                'enabled' => true,
            ],
        ],
    ],
    'lib' => [
        'core' => [
            // 'language' => 'en',
            'encoding' => 'UTF-8',
            'request' => [
                'csrf_meta' => 'csrf-token',
                'uri' => '/ajax',
            ],
            'prefix' => [
                'class' => '',
            ],
            'debug' => [
                'on' => false,
                'verbose' => false,
            ],
            'error' => [
                'handle' => false,
            ],
        ],
        'js' => [
            'lib' => [
                // 'uri' => '',
            ],
        ],
    ],
];
