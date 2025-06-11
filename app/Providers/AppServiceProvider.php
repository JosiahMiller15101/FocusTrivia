<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Dump the current scheme and full URL for debugging
        dd([
            'scheme' => request()->getScheme(),
            'full_url' => request()->fullUrl(),
            'is_secure' => request()->isSecure(),
        ]);

       if (config('app.env') === 'production') {
        URL::forceScheme('https');
    }
    }
}
