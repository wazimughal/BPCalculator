<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        $env = strtolower(config('app.env'));

        if (in_array($env, ['production', 'blue'], true)) {
            URL::forceScheme('https');
        }
    }

}
