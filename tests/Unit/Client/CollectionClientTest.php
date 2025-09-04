<?php

namespace DVB\Core\SDK\Tests\Unit\Client;

use DVB\Core\SDK\DvbApiClient;
use DVB\Core\SDK\DTOs\DeployCollectionRequestDTO;
use DVB\Core\SDK\DTOs\DeployCollectionResponseDTO;
use DVB\Core\SDK\DTOs\CollectionDetailResponseDTO;
use DVB\Core\SDK\DTOs\MintNftRequestDTO;
use DVB\Core\SDK\DTOs\MintNftResponseDTO;
use DVB\Core\SDK\Tests\TestCase;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Log\LoggerInterface;

class CollectionClientTest extends TestCase
{
    public function test_deploy_collection_with_image_returns_deploy_collection_response_dto(): void
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        $imageResource = fopen('php://memory', 'rb');
        $request = new DeployCollectionRequestDTO(
            chainId: 1,
            ownerAddress: '0xowner',
            name: 'Test Collection',
            quantity: 100,
            enableFlexibleMint: true,
            enableSoulbound: false,
            imageResource: $imageResource
        );
        
        $expectedResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'launchpad_id' => 'launchpad123'
            ]
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->with('POST', 'https://dev-epoch.nft-investment.io/api/remote/v1/collection', $this->callback(function ($options) {
                return isset($options['multipart']) && is_array($options['multipart']) && count($options['multipart']) > 0;
            }))
            ->willReturn(new Response(200, [], json_encode($expectedResponse)));
        
        // Act
        $result = $client->deployCollection($request);
        
        // Assert
        $this->assertInstanceOf(DeployCollectionResponseDTO::class, $result);
        $this->assertEquals(200, $result->code);
        $this->assertEquals('Success', $result->message);
        $this->assertEquals('launchpad123', $result->data);
        
        // Clean up
        fclose($imageResource);
    }
    
    public function test_get_collection_details_returns_collection_detail_response_dto(): void
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        $expectedResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'address' => '0x123',
                'name' => 'Test Collection'
            ]
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->with('GET', 'https://dev-epoch.nft-investment.io/api/remote/v1/collection/0x123', $this->callback(function ($options) {
                return isset($options['query']['chain_id']) && $options['query']['chain_id'] === 1;
            }))
            ->willReturn(new Response(200, [], json_encode($expectedResponse)));
        
        // Act
        $result = $client->getCollectionDetails('0x123', 1);
        
        // Assert
        $this->assertInstanceOf(CollectionDetailResponseDTO::class, $result);
        $this->assertEquals(200, $result->code);
        $this->assertEquals('Success', $result->message);
        $this->assertIsArray($result->data);
    }
    
    public function test_mint_nft_returns_mint_nft_response_dto(): void
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        $request = $this->createMock(MintNftRequestDTO::class);
        $request->method('toArray')->willReturn([
            'chain_id' => 1,
            'address' => '0x123',
            'to_address' => '0x456',
            'amount' => 1
        ]);
        
        $expectedResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'remote_job_id' => 'job123'
            ]
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->with('POST', 'https://dev-epoch.nft-investment.io/api/remote/v1/collection/mint-nft', $this->callback(function ($options) {
                return isset($options['json']) && is_array($options['json']);
            }))
            ->willReturn(new Response(200, [], json_encode($expectedResponse)));
        
        // Act
        $result = $client->mintNft($request);
        
        // Assert
        $this->assertInstanceOf(MintNftResponseDTO::class, $result);
        $this->assertEquals(200, $result->code);
        $this->assertEquals('Success', $result->message);
        $this->assertEquals('job123', $result->data);
    }
    
    public function test_get_collection_deploy_status_returns_collection_deploy_status_response_dto(): void
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        $expectedResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'status' => 'listing',
                'collection' => [
                    'chain_id' => 1,
                    'address' => '0x123',
                    'name' => 'Test Collection',
                    'symbol' => 'TEST',
                    'decimals' => 0,
                    'total_supply' => 1000,
                    'royalty' => 0.1,
                    'contract_type' => 'ERC721',
                    'is_flexible_mint' => true,
                    'is_jcd' => false,
                    'created_at' => 1234567890,
                    'updated_at' => 1234567890
                ]
            ]
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->with('GET', 'https://dev-epoch.nft-investment.io/api/remote/v1/collection/deploy-status/launchpad123')
            ->willReturn(new Response(200, [], json_encode($expectedResponse)));
        
        // Act
        $result = $client->getCollectionDeployStatus('launchpad123');
        
        // Assert
        $this->assertInstanceOf(\DVB\Core\SDK\DTOs\CollectionDeployStatusResponseDTO::class, $result);
        $this->assertEquals(200, $result->code);
        $this->assertEquals('Success', $result->message);
        $this->assertEquals(\DVB\Core\SDK\Enums\CollectionDeployStatusEnum::LISTING, $result->status);
        $this->assertInstanceOf(\DVB\Core\SDK\DTOs\CollectionDTO::class, $result->collection);
        $this->assertTrue($result->isDeployed());
    }
}