<?php

use App\Facade\RouteParser;
use App\Facade\Session;
use Lagdo\DbAdmin\Support\Facade\Auth;
use Lagdo\DbAdmin\Support\Provider;
use Lagdo\DbAdmin\Support\Service;
use Pharaonic\Slugify\Facades\Slugify;

return [
    'admin' => [
        'ui' => [
            'toast' => [
                'lib' => 'butterup',
            ],
            'query' => [
                // 'cm' for CodeMirror or 'ace' for Ace Editor.
                'editor' => 'cm',
            ],
        ],
        'queries' => [
            'save' => [
                'editor' => false,
                'builder' => false,
                'library' => false,
            ],
            'enable' => [
                'preferences' => false,
                'history' => false,
                'favorite' => false,
            ],
            'history' => [
                'distinct' => false,
                'limit' => 10,
            ],
            'favorite' => [
                'limit' => 10,
            ],
        ],
    ],
    'audit' => [
        'enabled' => false,
        'users' => [
            // The emails of users that are allowed to access the audit page.
        ],
        'queries' => [
            'database' => [
                // Same as the "servers" items, but "name" is the database name.
            ],
            'pagination' => [
                'limit' => 10,
            ],
        ],
    ],
    // 'auth' => null, // No auth.
    'auth' => fn() => new class implements Provider\AuthInterface {
        public function userId(): string
        {
            return Session::get('user')?->email ?? '';
        }
        public function name(): string
        {
            return Session::get('user')?->name ?? '';
        }
        public function roles(): array
        {
            return [];
        }
        public function audit(): string
        {
            return RouteParser::urlFor('dbaudit_page');
        }
        public function logout(): string
        {
            return RouteParser::urlFor('auth_logout');
        }
    },
    // 'export' => null, // No export.
    'export' => fn() => new class extends Service\Export\AbstractFileSystem {
        protected function storage(): string
        {
            return 'exports';
        }
        protected function path(string $filename): string
        {
            // Use the slugified username to customize the path.
            $userId = Auth::userId();
            $userDir = $userId === '' ? 'users' : 'users/' . Slugify::get($userId);
            return "$userDir/$filename";
        }
        protected function url(string $filename): string
        {
            return RouteParser::urlFor('export', ['filename' => $filename]);
        }
    },
    // Comment all to use the default secret config provider, which reads secret from the .env.dbadmin file.
    'secret' => [
    ],
];
