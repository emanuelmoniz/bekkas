<?php

namespace App\Providers;

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
        // Register Microsoft provider via SocialiteProviders if available
        if (class_exists(\SocialiteProviders\Manager\SocialiteWasCalled::class)) {
            $this->app['events']->listen(
                \SocialiteProviders\Manager\SocialiteWasCalled::class,
                'SocialiteProviders\\Microsoft\\MicrosoftExtendSocialite@handle'
            );
        }
    }
}
