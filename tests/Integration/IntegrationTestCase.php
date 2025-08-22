<?php

namespace DVB\Core\SDK\Tests\Integration;

use DVB\Core\SDK\DvbApiClient;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class IntegrationTestCase extends BaseTestCase
{
    protected DvbApiClient $client;

    /**
     * Set up the test case.
     *
     * @return void
     */
    protected function getPackageProviders($app)
    {
        return [
            \DVB\Core\SDK\DvbServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Create the client with the API key from environment variables
        $apiKey = env('DVB_API_KEY') ?? $_ENV['DVB_API_KEY'] ?? getenv('DVB_API_KEY') ?? null;
        $domain = env('DVB_API_DOMAIN') ?? $_ENV['DVB_API_DOMAIN'] ?? getenv('DVB_API_DOMAIN') ?? 'dev-epoch.nft-investment.io';
        
        if ($apiKey) {
            $this->client = new DvbApiClient(null, null, $apiKey, $domain);
        } else {
            $this->client = $this->app->make(DvbApiClient::class);
        }
    }

    /**
     * Get the DvbApiClient instance.
     *
     * @return DvbApiClient
     */
    protected function getClient(): DvbApiClient
    {
        return $this->client;
    }

    public static function isIntegrationTestEnabled(): bool
    {
        // Check multiple sources for environment variables
        $apiKey = env('DVB_API_KEY') ?? $_ENV['DVB_API_KEY'] ?? getenv('DVB_API_KEY') ?? null;
        $domain = env('DVB_API_DOMAIN') ?? $_ENV['DVB_API_DOMAIN'] ?? getenv('DVB_API_DOMAIN') ?? null;
        
        return !empty($apiKey) && !empty($domain);
    }
}