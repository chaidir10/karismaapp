<?php

if (
    !class_exists(\Intervention\Image\Facades\Image::class)
    && class_exists(\Intervention\Image\Laravel\Facades\Image::class)
) {
    class_alias(
        \Intervention\Image\Laravel\Facades\Image::class,
        \Intervention\Image\Facades\Image::class
    );
}

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\DetectDevice;
use App\Http\Middleware\LogActivity;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Alias → bisa dipakai di route
        $middleware->alias([
            'checkrole'     => CheckRole::class,
            'detectdevice'  => DetectDevice::class,
            'preventbackhistory' => \App\Http\Middleware\PreventBackHistory::class,
            'logactivity'   => LogActivity::class,
        ]);

        // Global middleware → otomatis jalan di semua request
        $middleware->append(DetectDevice::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
