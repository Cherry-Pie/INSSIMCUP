<?php

namespace App\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Collection::macro('sortForStandings', function() {
            return $this->sort(function($a, $b) {
                return $a->points === $b->points ? $b->goal_difference <=> $a->goal_difference : $b->points <=> $a->points;
            });
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
