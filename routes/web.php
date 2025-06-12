<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use PioneerDynamics\InertiaApiMiddleware\Http\Middleware\InertiaApiMiddleware;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified', '2fa'])->name('dashboard');

Route::middleware(['auth', 'verified', '2fa', InertiaApiMiddleware::class])->group(function() {
    // 
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
