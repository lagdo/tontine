<?php

return [
    'app' => [
        'metadata' => 'annotations',
        'faker' => env('APP_FAKER', false),
        'helpers' => true,
        'request' => [
            'route' => 'ajax',
            'middlewares' => [
                'web',
                'tontine',
                'annotations',
                'jaxon.config',
                'jaxon.ajax',
            ],
        ],
        'directories' => [
            [
                'path' => base_path('ajax/App'),
                'namespace' => '\\Ajax\\App',
            ],
        ],
        'views' => [
            // 'pagination' => [], // Is set by the TontineTemplate middleware
        ],
        'packages' => [
        ],
        'dialogs' => [
            'default' => [
                'modal' => 'bootbox',
                'alert' => 'toastr',
                'confirm' => 'noty',
            ],
            'toastr' => [
                'options' => [
                    'alert' => [
                        'closeButton' => true,
                        'closeDuration' => 0,
                        'positionClass' => 'toast-top-center',
                    ],
                ],
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
                'file' => env('JAXON_JS_APP_FILE', 'js-4.0.7'),
                'export' => env('JAXON_JS_APP_EXPORT', !env('APP_DEBUG')),
                'minify' => env('JAXON_JS_APP_MINIFY', !env('APP_DEBUG')),
            ],
        ],
    ],
];
