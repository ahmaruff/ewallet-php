<?php

namespace App\Providers;

use App\Services\PaymentGatewayService;
use Illuminate\Support\ServiceProvider;

class PaymentGatewayServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind the PaymentGatewayService to the container
        $this->app->singleton(PaymentGatewayService::class, function ($app) {
            return new PaymentGatewayService(
                config('services.payment_gateway.base_url'),
                config('services.payment_gateway.api_key'),
                $app->make('App\Services\LogService')
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
