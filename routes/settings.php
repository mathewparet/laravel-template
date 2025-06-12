<?php

use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\TwoFactorAuthenticationController;
use App\Http\Controllers\PasskeyController;

Route::middleware('auth')->group(function () {
    Route::redirect('settings', '/settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('settings/password', [PasswordController::class, 'update'])->name('password.update');

    Route::get('settings/2fa', [TwoFactorAuthenticationController::class, 'show'])->name('2fa.show');
    Route::get('settings/2fa/request', [TwoFactorAuthenticationController::class, 'request'])->name('2fa.request');
    Route::post('settings/2fa', [TwoFactorAuthenticationController::class, 'activate'])->name('2fa.activate');
    Route::post('settings/2fa/regenerate', [TwoFactorAuthenticationController::class, 'regenerateBackupCodes'])->name('2fa.regenerate');
    Route::get('settings/2fa/codes', [TwoFactorAuthenticationController::class, 'showBackupCodes'])->name('2fa.codes');
    Route::delete('settings/2fa', [TwoFactorAuthenticationController::class, 'disable'])->name('2fa.disable');

    Route::get('settings/passkey', [PasskeyController::class, 'index'])->name('passkey.index');
    
    Route::prefix(Config::get('passkey.routes.prefix', 'passkeys'))->group(function () {
        Route::middleware('password.confirm:,'.Config::get('passkey.password_confirmation_ttl'))->post('/registration-options', [PasskeyController::class, 'getRegistrationOptions'])->name('passkeys.registration-options');
        Route::post('/verify', [PasskeyController::class, 'verify'])->name('passkeys.verify');
        Route::middleware('password.confirm:,'.Config::get('passkey.password_confirmation_ttl'))->delete('/{passkey}', [PasskeyController::class, 'destroy'])->name('passkeys.destroy');
        Route::post('/', [PasskeyController::class, 'store'])->name('passkeys.store');
    });

    Route::get('settings/appearance', function () {
        return Inertia::render('settings/Appearance');
    })->name('appearance');
});

Route::prefix(Config::get('passkey.routes.prefix', 'passkeys'))->group(function () {
    Route::post('/authentication-options', [PasskeyController::class, 'getAuthenticationOptions'])->name('passkeys.authentication-options');
    Route::post('/login', [PasskeyController::class, 'login'])->name('passkeys.login');
});
