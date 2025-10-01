<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Authentik\AuthentikSDK;

class AuthentikServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AuthentikSDK::class, function ($app) {
            // Only create SDK if configuration is available
            $baseUrl = config('services.authentik.base_url');
            $apiToken = config('services.authentik.api_token');
            
            if (!$baseUrl || !$apiToken) {
                // Return a mock/null object or throw exception based on your needs
                return null;
            }
            
            return new AuthentikSDK();
        });

        $this->app->alias(AuthentikSDK::class, 'authentik');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}