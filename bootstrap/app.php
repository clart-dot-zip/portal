<?php

use App\Http\Middleware\RedirectIfUnauthenticated;
use App\Http\Middleware\CheckPortalAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth' => RedirectIfUnauthenticated::class,
            'portal.admin' => CheckPortalAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //

    })->create();
