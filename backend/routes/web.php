<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    //return Inertia::render('welcome');
    return response()->json([
        'aplicacion' => 'API Gestión de Usuarios',
        'estado' => 'en línea',
        'documentacion' => '/api',
    ]);
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
