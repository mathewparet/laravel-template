<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;
use Laravel\Scout\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->eagerLoadAllRelationships();

        $this->definedRequestMacros();
    }
    
    private function eagerLoadAllRelationships()
    {
        Model::automaticallyEagerLoadRelationships();
        Model::preventLazyLoading();
    }

    private function definedRequestMacros()
    {
        Request::macro('requestId', function() {
            /**
             * @var \Illuminate\Http\Request $this
             */
            return $this->headers->get('Request-Id');
        });
        
        Request::macro('sessionId', function() {
            /**
             * @var \Illuminate\Http\Request $this
             */
            return hash('sha256', $this->session()->getId());
        });
    }
}
