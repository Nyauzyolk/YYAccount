<?php

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
        $middleware->alias([
            '2fa' => \App\Http\Middleware\TwoFactorAuthMiddleware::class,
            'password.confirm' => \App\Http\Middleware\ConfirmPassword::class,
            'admin' => \App\Http\Middleware\RedirectIfNotAdmin::class,
            'admin.validateReferer' => \App\Http\Middleware\Admin\ValidateReferer::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
