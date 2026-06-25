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
        
        // 1. Pengaturan Alias Middleware (Kode Lama Kamu)
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleManager::class,
        ]);
        
        // 2. Pengecualian CSRF Token untuk Midtrans (Jalur VIP)
        $middleware->validateCsrfTokens(except: [
            'midtrans/callback',
            '*midtrans*'
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();