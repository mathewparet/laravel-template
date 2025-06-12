<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\TwoFactorAuthentication;
use App\Services\TwoFactorAuthentication\Google2FAService;
use Illuminate\Contracts\Support\DeferrableProvider;

class TwoFactorServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(TwoFactorAuthentication::class, Google2FAService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    public function provides()
    {
        return [
            TwoFactorAuthentication::class
        ];
    }
}
