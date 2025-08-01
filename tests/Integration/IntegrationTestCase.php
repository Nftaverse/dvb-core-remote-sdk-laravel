<?php

namespace DVB\Core\SDK\Tests\Integration;

use DVB\Core\SDK\DvbApiClient;
use DVB\Core\SDK\Tests\TestCase as BaseTestCase;

abstract class IntegrationTestCase extends BaseTestCase
{
    /**
     * The DvbApiClient instance.
     *
     * @var \DVB\Core\SDK\DvbApiClient|null
     */
    protected ?DvbApiClient $client = null;

    /**
     * Set up the test case.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Skip integration tests if API credentials are not configured
        if (!$this->isIntegrationTestEnabled()) {
            $this->markTestSkipped('Integration tests are disabled. Set DVB_API_KEY and DVB_API_BASE_URL to enable.');
        }

        $this->client = new DvbApiClient(
            null,
            null,
            $this->getApiKey(),
            $this->getBaseDomain(),
            $this->getProtocol()
        );
    }

    /**
     * Check if integration tests are enabled.
     *
     * @return bool
     */
    protected function isIntegrationTestEnabled(): bool
    {
        return !empty($this->getApiKey()) && !empty($this->getBaseDomain());
    }

    /**
     * Get the API key from environment variables.
     *
     * @return string
     */
    protected function getApiKey(): string
    {
        return $_ENV['DVB_API_KEY'] ?? $_SERVER['DVB_API_KEY'] ?? getenv('DVB_API_KEY') ?: '';
    }

    /**
     * Get the base domain from environment variables.
     *
     * @return string
     */
    protected function getBaseDomain(): string
    {
        return $_ENV['DVB_API_BASE_URL'] ?? $_SERVER['DVB_API_BASE_URL'] ?? getenv('DVB_API_BASE_URL') ?: 'dev-epoch.nft-investment.io';
    }

    /**
     * Get the protocol from environment variables.
     *
     * @return string
     */
    protected function getProtocol(): string
    {
        return $_ENV['DVB_API_PROTOCOL'] ?? $_SERVER['DVB_API_PROTOCOL'] ?? getenv('DVB_API_PROTOCOL') ?: 'https';
    }

    /**
     * Get the DvbApiClient instance.
     *
     * @return \DVB\Core\SDK\DvbApiClient
     */
    protected function getClient(): DvbApiClient
    {
        return $this->client;
    }
}