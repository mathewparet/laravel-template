<?php

namespace App\Providers;

use App\Models\User;
use Laravel\Pennant\Feature;
use Illuminate\Support\ServiceProvider;

class FeatureFlagServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        foreach(config('pennant.features') as $feature => $status) {
            Feature::define($feature, fn() => $status);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
