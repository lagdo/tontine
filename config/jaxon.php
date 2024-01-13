<?php

return [
    'app' => [
        'faker' => env('APP_FAKER', false),
        /*'request' => [
            'route' => 'ajax',
        ],*/
        'directories' => [
            app_path('Ajax/Web') => [
                'namespace' => '\\App\\Ajax\\Web',
                'classes' => [
                    '*' => [
                        'functions' => [
                            '*' => [
                                'callback' => 'jaxon.ajax.callback.tontine',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'views' => [
            'pagination' => [
                'directory' => resource_path('views/tontine/app/default/parts/table/pagination'),
                'extension' => '.blade.php',
                'renderer' => 'blade',
                // 'register' => true,
            ],
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
            'annotations' => [
                'enabled' => true,
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
                'file' => env('JAXON_JS_APP_FILE', 'js-2.6.7'),
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
