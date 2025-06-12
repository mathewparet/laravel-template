<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Client\Request;
use PioneerDynamics\InertiaApiMiddleware\Providers\InertiaApiServiceProvider;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->name('api.')->group(function() {
    
});