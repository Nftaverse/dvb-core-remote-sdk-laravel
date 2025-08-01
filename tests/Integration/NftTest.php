<?php

namespace DVB\Core\SDK\Tests\Integration;

use DVB\Core\SDK\DTOs\NftListResponseDTO;
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
            // Test with a common contract address and chain ID
            $response = $client->getNftsByContract('0x0000000000000000000000000000000000000000', 1);
            
            $this->assertInstanceOf(NftListResponseDTO::class, $response);
            $this->assertEquals(200, $response->code);
            $this->assertIsString($response->message);
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
            // Test with a common contract address, token ID and chain ID
            $response = $client->getNftMetadata('0x0000000000000000000000000000000000000000', '1', 1);
            
            $this->assertInstanceOf(NftMetadataResponseDTO::class, $response);
            $this->assertEquals(200, $response->code);
            $this->assertIsString($response->message);
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