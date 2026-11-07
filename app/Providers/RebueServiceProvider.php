<?php

namespace App\Providers;

use App\Services\SMSService;
use Illuminate\Support\ServiceProvider;

class RebueServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(SMSService::class, function ($app) {
            return new SMSService();
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
