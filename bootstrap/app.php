<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Inisialisasi konfigurasi utama aplikasi Laravel
return Application::configure(basePath: dirname(__DIR__))

    // Pengaturan routing utama aplikasi (web, console command, dan endpoint health check)
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    // Konfigurasi middleware yang digunakan pada lapisan aplikasi
    ->withMiddleware(function (Middleware $middleware): void {

        // Menambahkan middleware tambahan yang digunakan pada layer web
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        // Mendefinisikan alias middleware 'admin' untuk pengelolaan akses halaman administrator
        $middleware->alias([
            'admin' => \App\Http\Middleware\Admin::class,
        ]);
    })

    // Konfigurasi global untuk penanganan exception
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })

    // Membuat instance aplikasi Laravel agar siap dijalankan
    ->create();
