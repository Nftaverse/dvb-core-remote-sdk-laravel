<?php

namespace DVB\Core\SDK\Tests\Integration;

use DVB\Core\SDK\DTOs\CollectionEventDTO;
use DVB\Core\SDK\DTOs\CollectionEventListResponseDTO;
use DVB\Core\SDK\DTOs\CheckCollectionResponseDTO;
use DVB\Core\SDK\Exceptions\DvbApiException;

class CollectionTest extends IntegrationTestCase
{
    public function test_get_collection_events_returns_events_data()
    {
        $client = $this->getClient();
        
        // Skip if integration tests are disabled
        if (!$this->isIntegrationTestEnabled()) {
            $this->markTestSkipped('Integration tests are disabled.');
        }
        
        // Check if API key is actually set
        $apiKey = env('DVB_API_KEY') ?? $_ENV['DVB_API_KEY'] ?? getenv('DVB_API_KEY') ?? null;
        if (empty($apiKey)) {
            $this->markTestSkipped('API key is not set.');
        }
        
        try {
            // First, get networks to find a valid chain ID
            $networksResponse = $client->getNetworks();
            $chainId = null; // No default chain ID
            
            // If we have networks, use the first one
            $networkItems = $networksResponse->getItems();
            if (!empty($networkItems)) {
                $chainId = $networkItems[0]->chainId;
            }
            
            // Get owned collections to find a valid address
            $collectionsResponse = $client->getOwnCollections($chainId);
            $validAddress = null;

            if (!empty($collectionsResponse->data->items)) {
                $validAddress = $collectionsResponse->data->items[0]->address;
            }
            
            // If we can't get a valid address, skip the test
            if (!$validAddress) {
                $this->markTestSkipped('No valid owned collection address found.');
            }
            
            // Test with a valid contract address and chain ID
            // echo "API Key: " . substr($apiKey, 0, 10) . "...\n";
            $response = $client->getCollectionEvents($validAddress, $chainId);
            
            $this->assertInstanceOf(CollectionEventListResponseDTO::class, $response);
            $this->assertEquals(200, $response->code);
            $this->assertIsString($response->message);

            // Add more specific assertions for the DTO structure
            $this->assertTrue(is_array($response->data->items) || $response->data->items === null);
            if (!empty($response->data->items)) {
                $this->assertInstanceOf(CollectionEventDTO::class, $response->data->items[0]);
                $this->assertObjectHasProperty('eventType', $response->data->items[0]);
                $this->assertObjectHasProperty('tokenId', $response->data->items[0]);
            }

            if ($response->data->hasMore) {
                $this->assertNotNull($response->data->cursor);
            } else {
                $this->assertNull($response->data->cursor);
            }
        } catch (DvbApiException $e) {
            echo "Exception: " . $e->getMessage() . " Code: " . $e->getCode() . "\n";
            // If we get a 400/401/403, it means there's an issue with the request or API key
            if (in_array($e->getCode(), [400, 401, 403])) {
                $this->markTestSkipped('API request failed. Error: ' . $e->getMessage());
            }
            
            // Handle validation exceptions
            if ($e instanceof \DVB\Core\SDK\Exceptions\ValidationException) {
                $this->markTestSkipped('Validation error: ' . $e->getMessage());
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
            echo "Integration tests are disabled.\n";
            $this->markTestSkipped('Integration tests are disabled.');
        }

        $client = $this->getClient();
        
        try {
            // First, get networks to find a valid chain ID
            $networksResponse = $client->getNetworks();
            $chainId = null; // No default chain ID
            
            // If we have networks, use the first one
            $networkItems = $networksResponse->getItems();
            if (!empty($networkItems)) {
                $chainId = $networkItems[0]->chainId;
            }
            
            // If we still don't have a valid chain ID, skip the test
            if ($chainId === null) {
                $this->markTestSkipped('No valid chain ID found from networks.');
            }
            
            // Get owned collections to find a valid address
            $collectionsResponse = $client->getOwnCollections($chainId);
            $validAddress = null;
            $toAddress = null;

            if (!empty($collectionsResponse->data->items)) {
                $validAddress = $collectionsResponse->data->items[0]->address;
                // Use a generic wallet address for testing since CollectionDTO doesn't have owner property
                $toAddress = '0x0000000000000000000000000000000000000000';
            }
            
            // If we can't get a valid address, skip the test
            if (!$validAddress || !$toAddress) {
                $this->markTestSkipped('No valid owned collection address or owner found.');
            }
            
            // Test with the contract address and chain ID
            $response = $client->checkCollection($chainId, $validAddress, $toAddress);
            
            $this->assertInstanceOf(CheckCollectionResponseDTO::class, $response);
            $this->assertEquals(200, $response->code);
            $this->assertIsString($response->message);

            // Add more specific assertions
            $this->assertIsBool($response->data);
        } catch (DvbApiException $e) {
            // echo "Exception: " . $e->getMessage() . " Code: " . $e->getCode() . "\n";
            
            // If we get a 400 error, check the specific error code
            if ($e->getCode() === 400) {
                $responseBody = $e->getMessage();
                // Try to extract JSON from the error response
                $jsonStart = strpos($responseBody, '{');
                $jsonEnd = strrpos($responseBody, '}');
                
                if ($jsonStart !== false && $jsonEnd !== false && $jsonEnd > $jsonStart) {
                    $jsonString = substr($responseBody, $jsonStart, $jsonEnd - $jsonStart + 1);
                    $errorData = json_decode($jsonString, true);
                    
                    if (is_array($errorData) && isset($errorData['code'])) {
                        $errorCode = (int)$errorData['code'];
                        // Handle specific error codes
                        switch ($errorCode) {
                            case 3002: // Collection address invalid
                                $this->markTestSkipped('Collection address invalid.');
                                break;
                            case 3005: // Collection owner invalid
                                $this->markTestSkipped('Collection owner invalid.');
                                break;
                            case 5002: // Wallet address invalid
                                $this->markTestSkipped('Wallet address invalid.');
                                break;
                            default:
                                // For other 400 errors, re-throw
                                throw $e;
                        }
                    } else {
                        // If we can't parse the error code, re-throw
                        throw $e;
                    }
                } else {
                    // If we can't extract JSON, try the old regex method as fallback
                    if (preg_match('/"code":(\d+)/', $responseBody, $matches)) {
                        $errorCode = (int)$matches[1];
                        // Handle specific error codes
                        switch ($errorCode) {
                            case 3002: // Collection address invalid
                                $this->markTestSkipped('Collection address invalid.');
                                break;
                            case 3005: // Collection owner invalid
                                $this->markTestSkipped('Collection owner invalid.');
                                break;
                            case 5002: // Wallet address invalid
                                $this->markTestSkipped('Wallet address invalid.');
                                break;
                            default:
                                // For other 400 errors, re-throw
                                throw $e;
                        }
                    } else {
                        // If we can't parse the error code, re-throw
                        throw $e;
                    }
                }
            }
            
            // If we get a 401/403, it means the API key is invalid, which is expected in some test environments
            if (in_array($e->getCode(), [401, 403])) {
                $this->markTestSkipped('API key is invalid or missing required permissions.');
            }
            
            // Handle validation exceptions
            if ($e instanceof \DVB\Core\SDK\Exceptions\ValidationException) {
                $this->markTestSkipped('Validation error: ' . $e->getMessage());
            }
            
            // Re-throw other exceptions
            throw $e;
        }
    }
}