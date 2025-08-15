<?php

namespace DVB\Core\SDK;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class DvbServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->app->singleton(DvbApiClient::class, function ($app) {
            $config = $app['config']['services.dvb'];

            return new DvbApiClient(
                $app->make(ClientInterface::class),
                $app['log'],
                $config['api_key'] ?? '',
                $config['domain'] ?? 'dev-epoch.nft-investment.io',
                $config['protocol'] ?? 'https'
            );
        });

        $this->app->bind(ClientInterface::class, Client::class);
    }

    public function provides(): array
    {
        return [DvbApiClient::class, ClientInterface::class];
    }
}