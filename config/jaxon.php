<?php

return [
    'app' => [
        'metadata' => 'annotations',
        'faker' => env('APP_FAKER', false),
        'helpers' => true,
        'request' => [
            'route' => 'jaxon.ajax',
            'middlewares' => [
                'web',
                'tontine',
                'annotations',
                'analytics',
                'jaxon.config',
                'jaxon.ajax',
            ],
        ],
        // 'directories' => [],
        'views' => [
            // 'pagination' => [], // Is set by the TontineTemplate middleware
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
                // 'file' => env('JAXON_JS_APP_FILE', 'js-4.0.9'),
                'export' => env('JAXON_JS_APP_EXPORT', !env('APP_DEBUG')),
                'minify' => env('JAXON_JS_APP_MINIFY', !env('APP_DEBUG')),
            ],
        ],
    ],
];
