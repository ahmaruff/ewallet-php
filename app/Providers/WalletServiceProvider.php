<?php

namespace App\Providers;

use App\Services\LogService;
use App\Services\PaymentGatewayService;
use App\Services\WalletService;
use Illuminate\Support\ServiceProvider;

class WalletServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {  
        $this->app->singleton(WalletService::class, function ($app) {
            return new WalletService(
                $app->make(PaymentGatewayService::class), 
                $app->make(LogService::class)
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
