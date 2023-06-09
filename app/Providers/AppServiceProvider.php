<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public function boot()
    {
        app('db')->listen(function($query) {
            app('log')->info(
                $query->sql,
                $query->bindings,
                $query->time
            );
        });
    }
}
