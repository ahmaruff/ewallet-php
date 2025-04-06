<?php

namespace App\Providers;

use App\Exceptions\Handler;
use App\Services\AgentService;
use App\Services\LogService;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AgentService::class, function($app) {
            return new AgentService();
        });

        $this->app->bind(LogService::class, function($app) {
            return new LogService($app->make(AgentService::class));
        });
        
        $this->app->singleton(ExceptionHandler::class, Handler::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share the authenticated user globally
        Inertia::share([
            'user' => function () {
                return Auth::check() ? Auth::user()->load('roles') : null;
            },
        ]);
    }
}
