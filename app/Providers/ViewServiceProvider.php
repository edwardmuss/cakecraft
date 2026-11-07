<?php

namespace App\Providers;

use App\Helpers\SettingsHelper;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
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
        View::composer('*', function ($view) {
            // Retrieve settings
            $settings = SettingsHelper::getSettings();

            // Retrieve the account if the user is authenticated
            $current_branch = Auth::check() ? Branch::find(Auth::user()->branch_id) : null;

            // Share the settings and account data with all views
            $view->with([
                'settings' => $settings,
                'current_branch' => $current_branch,
            ]);
        });
    }
}
