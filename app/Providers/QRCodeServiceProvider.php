<?php

namespace App\Providers;

use App\Contracts\QRCodeGenerator;
use App\Services\QRCodeGenerator\BaconQRCodeGenerator;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class QRCodeServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(QRCodeGenerator::class, BaconQRCodeGenerator::class);
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
            QRCodeGenerator::class
        ];
    }
}
