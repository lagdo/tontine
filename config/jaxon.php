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
            'app' => [
                'uri' => env('JAXON_JS_APP_URI', '/jaxon'),
                'dir' => env('JAXON_JS_APP_DIR', public_path('/jaxon')),
                // The "file" option is set by the TontineJaxon middleware.
                // 'file' => env('JAXON_JS_APP_FILE', 'js-4.0.9'),
                'export' => env('JAXON_JS_APP_EXPORT', !env('APP_DEBUG')),
                'minify' => env('JAXON_JS_APP_MINIFY', !env('APP_DEBUG')),
            ],
        ],
    ],
];
