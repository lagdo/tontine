<?php

use App\Http\Middleware\TontineAnnotations;
use App\Http\Middleware\TontineHelper;
use App\Http\Middleware\TontineLocale;
use App\Http\Middleware\TontineTemplate;
use App\Http\Middleware\TontineTenant;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

require_once __DIR__ . '/errors.php';

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'annotations' => TontineAnnotations::class,
        ]);

        // Tontine middlewares
        $middleware->appendToGroup('tontine', [
            TontineTenant::class,
            TontineLocale::class,
            TontineTemplate::class,
            TontineHelper::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        App\Exceptions\handle($exceptions);
    })->create();
