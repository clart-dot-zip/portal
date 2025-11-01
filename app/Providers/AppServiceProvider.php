<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Services\Authentik\AuthentikSDK;
use App\Services\Pim\PimService;
use App\Services\Pim\ServerAccessManager;

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

        $this->app->singleton(ServerAccessManager::class, function ($app) {
            return new ServerAccessManager(
                config('pim.server', []),
                (bool) config('pim.dry_run', false)
            );
        });

        $this->app->singleton(PimService::class, function ($app) {
            return new PimService(
                $app->make(ServerAccessManager::class),
                config('pim', [])
            );
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
    }
}
