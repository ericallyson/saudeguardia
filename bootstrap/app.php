<?php

use App\Console\Commands\SendPendingMetaMessages;
use App\Http\Middleware\EnsureActiveSubscription;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        SendPendingMetaMessages::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'subscription.active' => EnsureActiveSubscription::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
