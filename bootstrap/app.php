<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckRole; // <-- Tambahkan use statement untuk middleware Anda

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Daftarkan alias middleware Anda di sini
        $middleware->alias([
            'role' => CheckRole::class, // <-- Daftarkan alias 'role'
            // alias middleware lain bisa ditambahkan di sini
        ]);

        // Anda juga bisa menambahkan middleware ke grup tertentu, misalnya 'web'
        // $middleware->web(append: [
        //     CheckRole::class, // Jika ingin diterapkan global ke grup web (hati-hati)
        // ]);
        
        // Atau menambahkan middleware secara global
        // $middleware->append(CheckRole::class); // Hati-hati, ini akan berlaku untuk semua route

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
