<?php

return [
    'binary' => env('CHROME_BINARY'),
    'browser' => [
        'startupTimeout' => 30,
    ],
    'page' => [
        'landscape'           => true,             // default to false
        'preferCSSPageSize'   => true,             // default to false (reads parameters directly from @page)
        'printBackground'     => true,             // default to false
        'scale'               => 0.9,              // defaults to 1.0 (must be a float)
        'displayHeaderFooter' => true,             // default to false
        'headerTemplate'      => '<div></div>',    // see https://pptr.dev/api/puppeteer.pdfoptions
        'footerTemplate'      => '<div></div>',    // see https://pptr.dev/api/puppeteer.pdfoptions
        'marginTop'           => 0.8,              // defaults to ~0.4 (must be a float, value in inches)
        'marginBottom'        => 0.6,              // defaults to ~0.4 (must be a float, value in inches)
        // 'marginLeft'          => 5.0,              // defaults to ~0.4 (must be a float, value in inches)
        // 'marginRight'         => 1.0,              // defaults to ~0.4 (must be a float, value in inches)
        // 'paperWidth'          => 6.0,              // defaults to 8.5 (must be a float, value in inches)
        // 'paperHeight'         => 6.0,              // defaults to 8.5 (must be a float, value in inches)
    ],
];
