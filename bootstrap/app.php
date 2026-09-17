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
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'monitor' => \App\Http\Middleware\MonitorMiddleware::class,
            'loket' => \App\Http\Middleware\LoketMiddleware::class,
            'arsip' => \App\Http\Middleware\ArsipMiddleware::class,
            'seksi1' => \App\Http\Middleware\Seksi1Middleware::class,
            'seksi2' => \App\Http\Middleware\Seksi2Middleware::class,
            'admin_or_monitor' => \App\Http\Middleware\AdminOrMonitorMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();