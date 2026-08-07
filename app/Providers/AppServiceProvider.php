<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Jenssegers\Agent\Agent;

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
        $agent = new Agent();
        if ($this->app->environment('production')) {
            \URL::forceScheme('https');
        }
        if (request()->isSecure()) {
            \URL::forceScheme('https');
        }

        View::share('isMobile', $agent->isMobile());
    }
}
