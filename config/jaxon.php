<?php

return [
    'app' => [
        'faker' => true,
        /*'request' => [
            'route' => 'ajax',
        ],*/
        'directories' => [
            app_path('Ajax/App') => [
                'namespace' => '\\App\\Ajax\\App',
            ],
        ],
        'views' => [
            'pagination' => [
                'directory' => resource_path('views/parts/table/pagination'),
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
                // 'uri' => env('JAXON_JS_APP_URI', ''),
                // 'dir' => env('JAXON_JS_APP_DIR', ''),
                // 'file' => env('JAXON_JS_APP_FILE', ''),
                // 'export' => env('JAXON_JS_APP_EXPORT', false),
                // 'minify' => env('JAXON_JS_APP_MINIFY', false),
                'export' => false,
                'minify' => false,
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
            'toastr' => [
                'options' => [
                    'closeButton' => true,
                    'closeDuration' => 0,
                    'positionClass' => 'toast-top-center'
                ],
            ],
        ],
    ],
];
