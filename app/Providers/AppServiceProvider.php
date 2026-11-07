<?php

namespace App\Providers;

use App\Events\InvoiceCreated;
use App\Events\InvoiceDelivered;
use App\Events\PaymentReceived;
use App\Listeners\SendInvoiceCreatedSMS;
use App\Listeners\SendInvoiceDeliveredSMS;
use App\Listeners\SendPaymentReceivedSMS;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();

        // Manually bind the listeners to the events
        Event::listen(
            InvoiceCreated::class,
            SendInvoiceCreatedSMS::class,
        );

        Event::listen(
            PaymentReceived::class,
            SendPaymentReceivedSMS::class,
        );

        Event::listen(
            InvoiceDelivered::class,
            SendInvoiceDeliveredSMS::class,
        );

        if (config('app.env') !== 'local' || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            URL::forceScheme('https');
        }

        View::composer('*', function ($view) {
            if (!Auth::check()) {
                return;
            }
        });
    }
}
