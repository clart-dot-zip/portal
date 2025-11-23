<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Services\Authentik\AuthentikSDK;
use App\Services\Pim\PimService;

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

        $this->app->singleton(PimService::class, function ($app) {
            return new PimService(config('pim', []));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(PimService $pimService): void
    {
        Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
            $event->extendSocialite('authentik', \SocialiteProviders\Authentik\Provider::class);
        });

        View::composer('layouts.navigation', function ($view) use ($pimService) {
            $user = Auth::user();
            $hasSelfServicePim = false;

            if ($user && $pimService->isEnabled() && $pimService->isOperational()) {
                $hasSelfServicePim = $user->hasAssignedPimGroups();
            }

            $view->with('hasSelfServicePim', $hasSelfServicePim);
        });
    }
}
