<?php

namespace App\Providers;

use App\Services\IPagClient;
use Illuminate\Support\ServiceProvider;

class IPagServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('ipag', function () {
            return new IPagClient(
                config('services.ipag.endpoint'),
                config('services.ipag.username'),
                config('services.ipag.password'),
            );
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
