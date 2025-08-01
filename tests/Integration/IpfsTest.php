<?php

namespace DVB\Core\SDK\Tests\Integration;

use DVB\Core\SDK\DTOs\IpfsStatsResponseDTO;
use DVB\Core\SDK\Exceptions\DvbApiException;

class IpfsTest extends IntegrationTestCase
{
    public function test_get_ipfs_stats_returns_stats_data()
    {
        // Skip if integration tests are disabled
        if (!$this->isIntegrationTestEnabled()) {
            $this->markTestSkipped('Integration tests are disabled.');
        }

        $client = $this->getClient();
        
        try {
            $response = $client->getIpfsStats();
            
            $this->assertInstanceOf(IpfsStatsResponseDTO::class, $response);
            $this->assertEquals(200, $response->code);
            $this->assertIsString($response->message);
            
            if ($response->data) {
                $this->assertIsInt($response->data->totalUploads);
                $this->assertIsInt($response->data->totalSize);
            }
        } catch (DvbApiException $e) {
            // If we get a 401/403, it means the API key is invalid, which is expected in some test environments
            if (in_array($e->getCode(), [401, 403])) {
                $this->markTestSkipped('API key is invalid or missing required permissions.');
            }
            
            // Re-throw other exceptions
            throw $e;
        }
    }
}