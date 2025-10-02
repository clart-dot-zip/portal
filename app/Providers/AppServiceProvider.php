<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Services\Authentik\AuthentikSDK;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AuthentikSDK::class, function ($app) {
            return new AuthentikSDK(config('services.authentik.api_token'));
        });

        $this->app->singleton(\App\Services\ApplicationAccessService::class, function ($app) {
            return new \App\Services\ApplicationAccessService($app->make(AuthentikSDK::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
            $event->extendSocialite('authentik', \SocialiteProviders\Authentik\Provider::class);
        });
        // Share Google Maps API key with all views
        View::share('googleMapsApiKey', env('GOOGLE_MAPS_API_KEY'));
    }
}
