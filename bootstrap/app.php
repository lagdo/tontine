<?php

require_once __DIR__ . '/errors.php';

use App\Http\Middleware\EnsureEmailIsVerified;
use App\Http\Middleware\TontineHelper;
use App\Http\Middleware\TontineJaxon;
use App\Http\Middleware\TontineLocale;
use App\Http\Middleware\TontineTemplate;
use App\Http\Middleware\TontineTenant;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;

use function App\Exceptions\handleExceptions;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
        $middleware->alias([
            'verified' => EnsureEmailIsVerified::class,
            'template' => TontineTemplate::class,
            'tenant' => TontineTenant::class,
        ]);

        // Tontine middlewares
        $middleware->appendToGroup('tontine', [
            TontineLocale::class,
            TontineTemplate::class,
            TontineJaxon::class,
            TontineHelper::class,
        ]);
    })
    ->withExceptions(handleExceptions(...))
    ->create();
