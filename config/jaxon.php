<?php

use App\Http\Middleware\TontineAnnotations;
use App\Http\Middleware\TontineLocale;
use App\Http\Middleware\TontineTemplate;
use App\Http\Middleware\TontineHtmlBuilder;
use App\Http\Middleware\TontineTenant;

return [
    'app' => [
        'metadata' => 'annotations',
        'faker' => env('APP_FAKER', false),
        'helpers' => true,
        'request' => [
            'route' => 'ajax',
            'middlewares' => [
                'web',
                TontineTenant::class,
                TontineLocale::class,
                TontineTemplate::class,
                TontineHtmlBuilder::class,
                TontineAnnotations::class,
                'jaxon.config',
                'jaxon.ajax',
            ],
        ],
        'directories' => [
            base_path('ajax/App') => [
                'namespace' => '\\Ajax\\App',
            ],
        ],
        'views' => [
            // 'pagination' => [], // Is set by the TontineTemplate middleware
        ],
        'packages' => [
        ],
    ],
    'lib' => [
        'core' => [
            'language' => 'en',
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
                'file' => env('JAXON_JS_APP_FILE', 'js-4.0.0-18'),
                'export' => env('JAXON_JS_APP_EXPORT', !env('APP_DEBUG')),
                'minify' => env('JAXON_JS_APP_MINIFY', !env('APP_DEBUG')),
            ],
        ],
        // 'assets' => [
        //     'include' => [
        //         'all' => true,
        //     ],
        // ],
        'dialogs' => [
            'default' => [
                'modal' => 'bootbox',
                'message' => 'toastr',
                'question' => 'noty',
            ],
            'bootbox' => [
                'assets' => [
                    'js' => "https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/6.0.0/bootbox.min.js",
                ],
            ],
            'toastr' => [
                'assets' => [
                    'js' => false,
                    'css' => false,
                ],
                'options' => [
                    'closeButton' => true,
                    'closeDuration' => 0,
                    'positionClass' => 'toast-top-center',
                ],
            ],
        ],
    ],
];
