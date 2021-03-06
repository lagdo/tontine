<?php

return [
    'app' => [
        /*'request' => [
            'route' => 'ajax',
        ],*/
        'directories' => [
            app_path('Ajax') => [
                'namespace' => '\\App\\Ajax',
                // 'separator' => '', // '.' or '_'
                // 'protected' => [],
                // 'autoload' => true,
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
        // 'packages' => [
        // ],
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
                // 'uri' => '',
                // 'dir' => '',
                // 'file' => '',
                // 'export' => true,
                // 'minify' => true,
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
