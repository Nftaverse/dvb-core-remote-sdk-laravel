<?php

namespace DVB\Core\SDK\Tests\Integration;

use DVB\Core\SDK\DTOs\CollectionEventListResponseDTO;
use DVB\Core\SDK\DTOs\CheckCollectionResponseDTO;
use DVB\Core\SDK\Exceptions\DvbApiException;

class CollectionTest extends IntegrationTestCase
{
    public function test_get_collection_events_returns_events_data()
    {
        // Skip if integration tests are disabled
        if (!$this->isIntegrationTestEnabled()) {
            $this->markTestSkipped('Integration tests are disabled.');
        }

        $client = $this->getClient();
        
        try {
            // Test with a common contract address and chain ID
            $response = $client->getCollectionEvents('0x0000000000000000000000000000000000000000', 1);
            
            $this->assertInstanceOf(CollectionEventListResponseDTO::class, $response);
            $this->assertEquals(200, $response->code);
            $this->assertIsString($response->message);
        } catch (DvbApiException $e) {
            // If we get a 401/403, it means the API key is invalid, which is expected in some test environments
            if (in_array($e->getCode(), [401, 403])) {
                $this->markTestSkipped('API key is invalid or missing required permissions.');
            }
            
            // Some collections might not exist, which is OK
            if ($e->getCode() === 404) {
                $this->markTestSkipped('Collection not found.');
            }
            
            // Re-throw other exceptions
            throw $e;
        }
    }
    
    public function test_check_collection_returns_validation_status()
    {
        // Skip if integration tests are disabled
        if (!$this->isIntegrationTestEnabled()) {
            $this->markTestSkipped('Integration tests are disabled.');
        }

        $client = $this->getClient();
        
        try {
            // Test with a common contract address and chain ID
            $response = $client->checkCollection(1, '0x0000000000000000000000000000000000000000', '0x0000000000000000000000000000000000000000');
            
            $this->assertInstanceOf(CheckCollectionResponseDTO::class, $response);
            $this->assertEquals(200, $response->code);
            $this->assertIsString($response->message);
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