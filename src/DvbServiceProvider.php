<?php

namespace DVB\Core\SDK;

use Illuminate\Support\ServiceProvider;

class DvbServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/dvb.php', 'dvb'
        );

        $this->app->singleton(DvbApiClient::class, function ($app) {
            return new DvbApiClient(
                null,
                $app['log'],
                config('dvb.key', config('services.dvb.key', '')),
                config('dvb.domain', config('services.dvb.domain', 'api.dvb.com')),
                config('dvb.protocol', config('services.dvb.protocol', 'https'))
            );
        });

        $this->app->alias(DvbApiClient::class, 'dvb-api-client');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/dvb.php' => config_path('dvb.php'),
        ], 'config');
    }
}