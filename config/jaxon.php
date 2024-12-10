<?php

use App\Http\Middleware\JaxonAnnotations;
use App\Http\Middleware\JaxonCallbacks;
use App\Http\Middleware\SetAppLocale;
use App\Http\Middleware\SetAppTemplate;
use App\Http\Middleware\TontineTenant;

return [
    'app' => [
        'metadata' => 'annotations',
        'faker' => env('APP_FAKER', false),
        'request' => [
            'route' => 'ajax',
            'middlewares' => [
                'web',
                TontineTenant::class,
                SetAppLocale::class,
                SetAppTemplate::class,
                JaxonAnnotations::class,
                JaxonCallbacks::class,
                'jaxon.ajax',
            ],
        ],
        'directories' => [
            base_path('ajax/App') => [
                'namespace' => '\\Ajax\\App',
            ],
        ],
        'views' => [
            // 'pagination' => [], // Is set by the SetAppTemplate middleware
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
                'file' => env('JAXON_JS_APP_FILE', 'js-4.0.0-12'),
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
                'question' => 'bootbox',
            ],
            'bootbox' => [
                'assets' => [
                    'js' => "https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/6.0.0/bootbox.min.js",
                ],
            ],
            'noty' => [
                'assets' => [
                    'js' => "https://cdn.jsdelivr.net/gh/needim/noty@2.4/js/noty/packaged/jquery.noty.packaged.min.js",
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
