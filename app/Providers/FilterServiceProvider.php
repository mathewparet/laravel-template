<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Str;

class FilterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->configureScoutBuilderMacro(\Laravel\Scout\Builder::class);
        
        $this->configureEloquentBuilderMacro(\Illuminate\Database\Eloquent\Builder::class);
    }
    
    private function configureScoutBuilderMacro($scoutBuilder)
    {
        $scoutBuilder::macro('filterFromUrl', function($default = null) {
            $query_string = request()->getQueryString();

            if($default) {
                parse_str($default, $defaults);
            }
            else {
                $defaults = [];
            }

            if(is_null($query_string) && count($defaults) === 0) return $this;

            parse_str($query_string ?? '', $filters);

            $filters = array_merge($defaults, $filters);

            $filters = is_array($filters) ? array_filter($filters, fn($value, $key) => !in_array($key, ['search', 'query', 'page']), ARRAY_FILTER_USE_BOTH) : [];

            $d = $this->query(fn($query) => $query);

            $d->query(function(EloquentBuilder $query) use($filters) {
                foreach($filters as $name => $value) {
                    if(in_array($name, $this->model::getFilterableArray())) {
                        if(!is_array($value)) {
                            $operator = match(substr($value, 0, 1))
                            {
                                '>' => '>',
                                '<' => '<',
                                '!' => '!=',
                                default => '='
                            };

                            $scope_method = Str::camel('scope_' . $name);
                
                            if($operator !== '=') {
                                $value = substr($value, 1);
                            }
                            
                            if(method_exists($this->model, $scope_method)) {
                                $this->model->{$scope_method}($query, $value);
                            }
                            else {
                                $query->where($name, $operator, $value);
                            }
                
                        }
                        else {
                            $query->whereBetween($name, $value);
                        }
                    }
                }
            });

            return $this;
        });
    }
    
    private function configureEloquentBuilderMacro($eloquentBuilder)
    {
        $eloquentBuilder::macro('filterFromUrl', function($default = null) {
            $query_string = request()->getQueryString();

            if($default) {
                parse_str($default, $defaults);
            }
            else {
                $defaults = [];
            }

            if(is_null($query_string) && count($defaults) === 0) return $this;

            parse_str($query_string ?? '', $filters);

            $filters = array_merge($defaults, $filters);

            $filters = is_array($filters) ? array_filter($filters, fn($value, $key) => !in_array($key, ['search', 'query', 'page']), ARRAY_FILTER_USE_BOTH) : [];

            $d = $this;
            // $d = $this->query(fn($query) => $query);

            $d->where(function(EloquentBuilder $query) use($filters) {
                foreach($filters as $name => $value) {
                    if(in_array($name, $this->model::getFilterableArray())) {
                        if(!is_array($value)) {
                            $operator = match(substr($value, 0, 1))
                            {
                                '>' => '>',
                                '<' => '<',
                                '!' => '!=',
                                default => '='
                            };

                            $scope_method = Str::camel('scope_' . $name);
                
                            if($operator !== '=') {
                                $value = substr($value, 1);
                            }
                            
                            if(method_exists($this->model, $scope_method)) {
                                $this->model->{$scope_method}($query, $value);
                            }
                            else {
                                $query->where($name, $operator, $value);
                            }
                
                        }
                        else {
                            $query->whereBetween($name, $value);
                        }
                    }
                }
            });

            return $this;
        });
    }
}
