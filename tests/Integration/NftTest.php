<?php

namespace DVB\Core\SDK\Tests\Integration;

use DVB\Core\SDK\DTOs\NftDTO;
use DVB\Core\SDK\DTOs\NftListResponseDTO;
use DVB\Core\SDK\DTOs\NftMetadataDTO;
use DVB\Core\SDK\DTOs\NftMetadataResponseDTO;
use DVB\Core\SDK\Exceptions\DvbApiException;

class NftTest extends IntegrationTestCase
{
    public function test_get_nfts_by_contract_returns_nfts_data()
    {
        // Skip if integration tests are disabled
        if (!$this->isIntegrationTestEnabled()) {
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
            
            // First, get a list of collections to find a valid address
            $collectionsResponse = $client->getCollections($chainId);
            $validAddress = null;
            
            if (!empty($collectionsResponse->data->items)) {
                $validAddress = $collectionsResponse->data->items[0]->address;
            }
            
            // If we can't get a valid address, skip the test
            if (!$validAddress) {
                $this->markTestSkipped('No valid collection address found.');
            }
            
            // Test with a valid contract address and chain ID
            $response = $client->getNftsByContract($validAddress, $chainId);
            
            $this->assertInstanceOf(NftListResponseDTO::class, $response);
            $this->assertEquals(200, $response->code);
            $this->assertIsString($response->message);

            // Add more specific assertions
            $this->assertIsArray($response->data->items);
            if (!empty($response->data->items)) {
                $this->assertInstanceOf(NftDTO::class, $response->data->items[0]);
                $this->assertObjectHasProperty('tokenId', $response->data->items[0]);
                $this->assertObjectHasProperty('metadata', $response->data->items[0]);
            }

            if ($response->data->hasMore) {
                $this->assertNotNull($response->data->cursor);
            } else {
                $this->assertNull($response->data->cursor);
            }
        } catch (DvbApiException $e) {
            // If we get a 401/403, it means the API key is invalid, which is expected in some test environments
            if (in_array($e->getCode(), [401, 403])) {
                $this->markTestSkipped('API key is invalid or missing required permissions.');
            }
            
            // Some contracts might not exist, which is OK
            if ($e->getCode() === 404) {
                $this->markTestSkipped('Contract not found.');
            }
            
            // Re-throw other exceptions
            throw $e;
        }
    }
    
    public function test_get_nft_metadata_returns_metadata()
    {
        // Skip if integration tests are disabled
        if (!$this->isIntegrationTestEnabled()) {
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
            
            // First, get a list of collections to find a valid address
            $collectionsResponse = $client->getCollections($chainId);
            $validAddress = null;
            
            if (!empty($collectionsResponse->data->items)) {
                $validAddress = $collectionsResponse->data->items[0]->address;
            }
            
            // If we can't get a valid address, skip the test
            if (!$validAddress) {
                $this->markTestSkipped('No valid collection address found.');
            }
            
            // Test with a valid contract address, token ID and chain ID
            // Using token ID '1' as a common test value
            $response = $client->getNftMetadata($validAddress, '1', $chainId);
            
            $this->assertInstanceOf(NftMetadataResponseDTO::class, $response);
            $this->assertEquals(200, $response->code);
            $this->assertIsString($response->message);

            // Add more specific assertions
            $this->assertInstanceOf(NftMetadataDTO::class, $response->data);
            $this->assertObjectHasProperty('name', $response->data);
            $this->assertObjectHasProperty('description', $response->data);
            $this->assertObjectHasProperty('image', $response->data);
        } catch (DvbApiException $e) {
            // If we get a 401/403, it means the API key is invalid, which is expected in some test environments
            if (in_array($e->getCode(), [401, 403])) {
                $this->markTestSkipped('API key is invalid or missing required permissions.');
            }
            
            // Some NFTs might not exist, which is OK
            if ($e->getCode() === 404) {
                $this->markTestSkipped('NFT not found.');
            }
            
            // Re-throw other exceptions
            throw $e;
        }
    }
}