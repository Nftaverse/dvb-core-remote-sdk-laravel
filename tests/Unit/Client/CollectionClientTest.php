<?php

namespace DVB\Core\SDK\Tests\Unit\Client;

use DVB\Core\SDK\Client\CollectionClient;
use DVB\Core\SDK\DTOs\DeployCollectionRequestDTO;
use DVB\Core\SDK\DTOs\DeployCollectionResponseDTO;
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
        
        $client = new CollectionClient($httpClient, $logger, 'test-key');
        
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
    
    public function test_deploy_collection_with_image_throws_exception_when_image_is_missing(): void
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new CollectionClient($httpClient, $logger, 'test-key');
        
        // Create a request with a closed image resource
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
        
        // Close the resource to make it invalid
        fclose($imageResource);
        
        // Mock the HTTP client to throw an exception when trying to send the request
        $httpClient->expects($this->once())
            ->method('request')
            ->willThrowException(new \GuzzleHttp\Exception\RequestException(
                'Invalid JSON response from API',
                $this->createMock(\Psr\Http\Message\RequestInterface::class)
            ));
        
        // Assert
        $this->expectException(\DVB\Core\SDK\Exceptions\DvbApiException::class);
        
        // Act
        $client->deployCollection($request);
    }
}