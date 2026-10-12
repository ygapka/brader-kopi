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
        if (app()->environment('production') || str_contains(request()->getHost(), 'vercel.app')) {
            URL::forceScheme('https');
        }

        if (str_contains(request()->getHost(), 'vercel.app') || env('ASSET_URL')) {
            config(['app.asset_url' => env('ASSET_URL', '/public')]);
        }
    }
}
