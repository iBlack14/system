<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Auto-verificar y crear directorios esenciales de storage para cPanel / hosting compartido
$requiredStorageDirs = [
    dirname(__DIR__).'/storage/framework/views',
    dirname(__DIR__).'/storage/framework/cache/data',
    dirname(__DIR__).'/storage/framework/sessions',
    dirname(__DIR__).'/storage/framework/testing',
    dirname(__DIR__).'/storage/logs',
    dirname(__DIR__).'/storage/app/public',
    dirname(__DIR__).'/bootstrap/cache',
];
foreach ($requiredStorageDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Cloudflare Quick Tunnel terminates HTTPS before forwarding to Laravel.
        // Trust its forwarded scheme so asset() and redirects keep HTTPS.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
