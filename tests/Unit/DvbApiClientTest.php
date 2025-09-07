<?php

namespace DVB\Core\SDK\Tests\Unit;

use DVB\Core\SDK\DvbApiClient;
use DVB\Core\SDK\Tests\TestCase;
use DVB\Core\SDK\Exceptions\DvbApiException;
use DVB\Core\SDK\DTOs\UserResponseDTO;
use DVB\Core\SDK\DTOs\PermissionsResponseDTO;
use DVB\Core\SDK\DTOs\CheckPermissionResponseDTO;
use DVB\Core\SDK\DTOs\ApiResponse;
use DVB\Core\SDK\DTOs\CollectionEventListResponseDTO;
use DVB\Core\SDK\DTOs\CheckCollectionResponseDTO;
use DVB\Core\SDK\DTOs\NftListResponseDTO;
use DVB\Core\SDK\DTOs\NftMetadataResponseDTO;
use DVB\Core\SDK\DTOs\NftJobDetailsResponseDTO;
use DVB\Core\SDK\DTOs\WebhookListResponseDTO;
use DVB\Core\SDK\DTOs\WebhookDetailsResponseDTO;
use DVB\Core\SDK\DTOs\CreatePaymentResponseDTO;
use DVB\Core\SDK\DTOs\PaymentDetailsResponseDTO;
use DVB\Core\SDK\DTOs\PaymentGatewayResponseDTO;
use DVB\Core\SDK\DTOs\PaymentMethodListResponseDTO;
use DVB\Core\SDK\DTOs\NetworkListResponseDTO;
use DVB\Core\SDK\DTOs\NetworkDetailResponseDTO;
use DVB\Core\SDK\DTOs\IpfsUploadResponseDTO;
use DVB\Core\SDK\DTOs\IpfsFolderUploadResponseDTO;
use DVB\Core\SDK\DTOs\IpfsStatsResponseDTO;
use DVB\Core\SDK\Enums\WebhookType;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;

class DvbApiClientTest extends TestCase
{
    public function test_it_can_be_instantiated(): void
    {
        $client = new DvbApiClient();
        
        $this->assertInstanceOf(DvbApiClient::class, $client);
    }

    public function test_it_can_be_instantiated_with_dependencies(): void
    {
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key', 'test-domain', 'http');

        $this->assertInstanceOf(DvbApiClient::class, $client);
        $this->assertEquals('test-key', $client->getApiKey());
        $this->assertEquals('test-domain', $client->getBaseDomain());
        $this->assertEquals('http', $client->getProtocol());
    }

    public function test_get_profile_returns_user_response_dto(): void
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        $expectedResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'uid' => 'user123',
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'avatar' => 'https://example.com/avatar.jpg',
                'wallet' => [
                    ['wallet_address' => '0x123', 'chainId' => 1],
                ],
                'createdAt' => '2023-01-01T00:00:00Z',
                'updatedAt' => '2023-01-02T00:00:00Z',
            ]
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->with('GET', 'https://dev-epoch.nft-investment.io/api/remote/v1/profile', $this->callback(function ($options) {
                return isset($options['headers']['Authorization']) && 
                       $options['headers']['Authorization'] === 'Bearer test-key' &&
                       $options['headers']['Accept'] === 'application/json' &&
                       $options['headers']['Content-Type'] === 'application/json';
            }))
            ->willReturn(new Response(200, [], json_encode($expectedResponse)));
        
        // Act
        $result = $client->getProfile();
        
        // Assert
        $this->assertInstanceOf(UserResponseDTO::class, $result);
        $this->assertEquals(200, $result->code);
        $this->assertEquals('Success', $result->message);
        $this->assertInstanceOf(\DVB\Core\SDK\DTOs\UserDTO::class, $result->data);
        $this->assertEquals('user123', $result->data->uid);
        $this->assertEquals('John Doe', $result->data->name);
        $this->assertEquals('john@example.com', $result->data->email);
        $this->assertEquals('https://example.com/avatar.jpg', $result->data->avatar);
        $this->assertIsArray($result->data->wallet);
        $this->assertInstanceOf(\DVB\Core\SDK\DTOs\WalletDTO::class, $result->data->wallet[0]);
        $this->assertEquals('0x123', $result->data->wallet[0]->wallet_address);
        $this->assertFalse($result->data->wallet[0]->is_treasury);
    }

    public function test_get_profile_throws_exception_on_api_error()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'invalid-key');
        
        $httpClient->expects($this->once())
            ->method('request')
            ->willThrowException(new RequestException(
                'Unauthorized',
                $this->createMock(RequestInterface::class)
            ));
        
        // Assert
        $this->expectException(DvbApiException::class);
        
        // Act
        $client->getProfile();
    }

    public function test_query_user_returns_user_response_dto()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        $expectedResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'uid' => 'user456',
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
            ]
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->with('POST', 'https://dev-epoch.nft-investment.io/api/remote/v1/query/user', $this->callback(function ($options) {
                return isset($options['json']['field']) && $options['json']['field'] === 'email' &&
                       isset($options['json']['value']) && $options['json']['value'] === 'jane@example.com' &&
                       isset($options['headers']['Authorization']) && 
                       $options['headers']['Authorization'] === 'Bearer test-key';
            }))
            ->willReturn(new Response(200, [], json_encode($expectedResponse)));
        
        // Act
        $result = $client->queryUser('email', 'jane@example.com');
        
        // Assert
        $this->assertInstanceOf(UserResponseDTO::class, $result);
        $this->assertEquals(200, $result->code);
        $this->assertEquals('Success', $result->message);
        $this->assertInstanceOf(\DVB\Core\SDK\DTOs\UserDTO::class, $result->data);
        $this->assertEquals('user456', $result->data->uid);
        $this->assertEquals('Jane Smith', $result->data->name);
        $this->assertEquals('jane@example.com', $result->data->email);
    }

    public function test_get_permissions_returns_permissions_response_dto()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        $expectedResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'permissions' => ['read', 'write', 'admin']
            ]
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->with('GET', 'https://dev-epoch.nft-investment.io/api/remote/v1/permission')
            ->willReturn(new Response(200, [], json_encode($expectedResponse)));
        
        // Act
        $result = $client->getPermissions();
        
        // Assert
        $this->assertInstanceOf(PermissionsResponseDTO::class, $result);
        $this->assertEquals(200, $result->code);
        $this->assertEquals('Success', $result->message);
        $this->assertIsArray($result->data);
        $this->assertEquals(['read', 'write', 'admin'], $result->data);
    }

    public function test_check_permission_returns_check_permission_response_dto()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        $expectedResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'hasPermission' => true
            ]
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->with('POST', 'https://dev-epoch.nft-investment.io/api/remote/v1/permission/check', $this->callback(function ($options) {
                return isset($options['json']['permission']) && 
                       $options['json']['permission'] === ['read', 'write'];
            }))
            ->willReturn(new Response(200, [], json_encode($expectedResponse)));
        
        // Act
        $result = $client->checkPermission(['read', 'write']);
        
        // Assert
        $this->assertInstanceOf(CheckPermissionResponseDTO::class, $result);
        $this->assertEquals(200, $result->code);
        $this->assertEquals('Success', $result->message);
        $this->assertTrue($result->data);
    }

    public function test_send_email_returns_api_response()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        $expectedResponse = [
            'code' => 200,
            'message' => 'Email sent successfully'
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->with('POST', 'https://dev-epoch.nft-investment.io/api/remote/v1/send-email', $this->callback(function ($options) {
                return isset($options['json']['email']) && $options['json']['email'] === 'test@example.com' &&
                       isset($options['json']['subject']) && $options['json']['subject'] === 'Test Subject' &&
                       isset($options['json']['body']) && $options['json']['body'] === 'Test Body';
            }))
            ->willReturn(new Response(200, [], json_encode($expectedResponse)));
        
        // Act
        $result = $client->sendEmail('test@example.com', 'Test Subject', 'Test Body');
        
        // Assert
        $this->assertInstanceOf(ApiResponse::class, $result);
        $this->assertEquals(200, $result->code);
        $this->assertEquals('Email sent successfully', $result->message);
    }

    public function test_send_sms_returns_api_response()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        $expectedResponse = [
            'code' => 200,
            'message' => 'SMS sent successfully'
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->with('POST', 'https://dev-epoch.nft-investment.io/api/remote/v1/send-sms', $this->callback(function ($options) {
                return isset($options['json']['phone']) && $options['json']['phone'] === '+1234567890' &&
                       isset($options['json']['body']) && $options['json']['body'] === 'Test SMS';
            }))
            ->willReturn(new Response(200, [], json_encode($expectedResponse)));
        
        // Act
        $result = $client->sendSms('+1234567890', 'Test SMS');
        
        // Assert
        $this->assertInstanceOf(ApiResponse::class, $result);
        $this->assertEquals(200, $result->code);
        $this->assertEquals('SMS sent successfully', $result->message);
    }

    public function test_get_collection_events_returns_collection_event_list_response_dto()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        $expectedResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'items' => [],
                'cursor' => null,
                'hasMore' => false
            ]
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->with('GET', 'https://dev-epoch.nft-investment.io/api/remote/v1/collection/0x123/event', $this->callback(function ($options) {
                return isset($options['query']['chain_id']) && $options['query']['chain_id'] === 1;
            }))
            ->willReturn(new Response(200, [], json_encode($expectedResponse)));
        
        // Act
        $result = $client->getCollectionEvents('0x123', 1);
        
        // Assert
        $this->assertInstanceOf(CollectionEventListResponseDTO::class, $result);
        $this->assertEquals(200, $result->code);
        $this->assertIsArray($result->data->items);
        $this->assertNull($result->data->cursor);
        $this->assertFalse($result->data->hasMore);
    }

    public function test_check_collection_returns_check_collection_response_dto()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        $expectedResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => true
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->with('POST', 'https://dev-epoch.nft-investment.io/api/remote/v1/collection/check', $this->callback(function ($options) {
                return isset($options['query']['chain_id']) && $options['query']['chain_id'] === 1 &&
                       isset($options['query']['address']) && $options['query']['address'] === '0x123' &&
                       isset($options['query']['to_address']) && $options['query']['to_address'] === '0x456';
            }))
            ->willReturn(new Response(200, [], json_encode($expectedResponse)));
        
        // Act
        $result = $client->checkCollection(1, '0x123', '0x456');
        
        // Assert
        $this->assertInstanceOf(CheckCollectionResponseDTO::class, $result);
        $this->assertEquals(200, $result->code);
        $this->assertEquals('Success', $result->message);
        $this->assertTrue($result->data);
    }

    public function test_get_nfts_by_contract_returns_nft_list_response_dto()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        $expectedResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'items' => [],
                'cursor' => null,
                'hasMore' => false
            ]
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->with('GET', 'https://dev-epoch.nft-investment.io/api/remote/v1/nft/0x123', $this->callback(function ($options) {
                return isset($options['query']['chain_id']) && $options['query']['chain_id'] === 1;
            }))
            ->willReturn(new Response(200, [], json_encode($expectedResponse)));
        
        // Act
        $result = $client->getNftsByContract('0x123', 1);
        
        // Assert
        $this->assertInstanceOf(NftListResponseDTO::class, $result);
        $this->assertEquals(200, $result->code);
        $this->assertIsArray($result->data->items);
        $this->assertNull($result->data->cursor);
        $this->assertFalse($result->data->hasMore);
    }

    public function test_get_nfts_by_contract_with_cursor_returns_nft_list_response_dto()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        $expectedResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'items' => [],
                'cursor' => 'next_cursor',
                'hasMore' => true
            ]
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->with('GET', 'https://dev-epoch.nft-investment.io/api/remote/v1/nft/0x123', $this->callback(function ($options) {
                return isset($options['query']['chain_id']) && $options['query']['chain_id'] === 1 &&
                       isset($options['query']['cursor']) && $options['query']['cursor'] === 'test_cursor';
            }))
            ->willReturn(new Response(200, [], json_encode($expectedResponse)));
        
        // Act
        $result = $client->getNftsByContract('0x123', 1, 'test_cursor');
        
        // Assert
        $this->assertInstanceOf(NftListResponseDTO::class, $result);
        $this->assertEquals(200, $result->code);
        $this->assertIsArray($result->data->items);
        $this->assertEquals('next_cursor', $result->data->cursor);
        $this->assertTrue($result->data->hasMore);
    }

    public function test_get_nft_metadata_returns_nft_metadata_response_dto()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        $expectedResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'name' => 'Test NFT',
                'description' => 'Test Description',
                'image' => 'https://example.com/image.png'
            ]
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->with('GET', 'https://dev-epoch.nft-investment.io/api/remote/v1/nft/0x123/token123/metadata', $this->callback(function ($options) {
                return isset($options['query']['chain_id']) && $options['query']['chain_id'] === 1;
            }))
            ->willReturn(new Response(200, [], json_encode($expectedResponse)));
        
        // Act
        $result = $client->getNftMetadata('0x123', 'token123', 1);
        
        // Assert
        $this->assertInstanceOf(NftMetadataResponseDTO::class, $result);
        $this->assertEquals(200, $result->code);
        $this->assertEquals('Test NFT', $result->data->name);
        $this->assertEquals('Test Description', $result->data->description);
        $this->assertEquals('https://example.com/image.png', $result->data->image);
    }

    public function test_get_nft_job_details_returns_nft_job_details_response_dto()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        $expectedResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'jobId' => 'job123',
                'status' => 'completed'
            ]
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->with('GET', 'https://dev-epoch.nft-investment.io/api/remote/v1/nft/details/job123')
            ->willReturn(new Response(200, [], json_encode($expectedResponse)));
        
        // Act
        $result = $client->getNftJobDetails('job123');
        
        // Assert
        $this->assertInstanceOf(NftJobDetailsResponseDTO::class, $result);
        $this->assertEquals(200, $result->code);
        $this->assertEquals('job123', $result->data->jobId);
        $this->assertEquals('completed', $result->data->status);
    }

    public function test_create_nft_event_returns_check_collection_response_dto()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        $expectedResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => true
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->with('POST', 'https://dev-epoch.nft-investment.io/api/remote/v1/nft/event', $this->callback(function ($options) {
                return isset($options['json']['eventType']) && $options['json']['eventType'] === 'transfer';
            }))
            ->willReturn(new Response(200, [], json_encode($expectedResponse)));
        
        // Act
        $result = $client->createNftEvent(['eventType' => 'transfer']);
        
        // Assert
        $this->assertInstanceOf(CheckCollectionResponseDTO::class, $result);
        $this->assertEquals(200, $result->code);
        $this->assertEquals('Success', $result->message);
        $this->assertTrue($result->data);
    }

    public function test_get_webhooks_returns_webhook_list_response_dto()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        $expectedResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'items' => [],
                'cursor' => null,
                'hasMore' => false
            ]
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->with('GET', 'https://dev-epoch.nft-investment.io/api/remote/v1/webhook')
            ->willReturn(new Response(200, [], json_encode($expectedResponse)));
        
        // Act
        $result = $client->getWebhooks();
        
        // Assert
        $this->assertInstanceOf(WebhookListResponseDTO::class, $result);
        $this->assertEquals(200, $result->code);
        $this->assertIsArray($result->data->items);
        $this->assertNull($result->data->cursor);
        $this->assertFalse($result->data->hasMore);
    }

    public function test_create_webhook_with_valid_data_returns_webhook_list_response_dto()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        $expectedResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'items' => [],
                'cursor' => null,
                'hasMore' => false
            ]
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->with('POST', 'https://dev-epoch.nft-investment.io/api/remote/v1/webhook', $this->callback(function ($options) {
                return isset($options['json']['url']) && $options['json']['url'] === 'https://example.com/webhook' &&
                       isset($options['json']['type']) && $options['json']['type'] === 'MINT_NFT' &&
                       isset($options['json']['collectionAddress']) && $options['json']['collectionAddress'] === '0x123' &&
                       isset($options['json']['collectionChainId']) && $options['json']['collectionChainId'] === '1';
            }))
            ->willReturn(new Response(200, [], json_encode($expectedResponse)));
        
        // Act
        $result = $client->createWebhook('https://example.com/webhook', WebhookType::mint_nft, null, '0x123', '1');
        
        // Assert
        $this->assertInstanceOf(WebhookListResponseDTO::class, $result);
        $this->assertEquals(200, $result->code);
        $this->assertIsArray($result->data->items);
        $this->assertNull($result->data->cursor);
        $this->assertFalse($result->data->hasMore);
    }

    public function test_create_webhook_without_required_collection_data_throws_exception()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        // Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Collection address and chain ID are required for this webhook type.');
        
        // Act
        $client->createWebhook('https://example.com/webhook', WebhookType::mint_nft);
    }

    public function test_create_webhook_with_non_collection_type_does_not_require_collection_data()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        $expectedResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'items' => [],
                'cursor' => null,
                'hasMore' => false
            ]
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->with('POST', 'https://dev-epoch.nft-investment.io/api/remote/v1/webhook', $this->callback(function ($options) {
                return isset($options['json']['url']) && $options['json']['url'] === 'https://example.com/webhook' &&
                       isset($options['json']['type']) && $options['json']['type'] === 'USER_PROFILE_UPDATE';
            }))
            ->willReturn(new Response(200, [], json_encode($expectedResponse)));
        
        // Act
        $result = $client->createWebhook('https://example.com/webhook', WebhookType::user_profile_update);
        
        // Assert
        $this->assertInstanceOf(WebhookListResponseDTO::class, $result);
        $this->assertEquals(200, $result->code);
        $this->assertIsArray($result->data->items);
        $this->assertNull($result->data->cursor);
        $this->assertFalse($result->data->hasMore);
    }

    public function test_get_webhook_returns_webhook_details_response_dto()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        $expectedResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'id' => 'webhook123',
                'url' => 'https://example.com/webhook'
            ]
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->with('GET', 'https://dev-epoch.nft-investment.io/api/remote/v1/webhook/webhook123')
            ->willReturn(new Response(200, [], json_encode($expectedResponse)));
        
        // Act
        $result = $client->getWebhook('webhook123');
        
        // Assert
        $this->assertInstanceOf(WebhookDetailsResponseDTO::class, $result);
        $this->assertEquals(200, $result->code);
        $this->assertEquals('webhook123', $result->data->id);
        $this->assertEquals('https://example.com/webhook', $result->data->url);
    }

    public function test_delete_webhook_returns_webhook_details_response_dto()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        $expectedResponse = [
            'code' => 200,
            'message' => 'Webhook deleted',
            'data' => [
                'id' => 'webhook123',
                'url' => 'https://example.com/webhook'
            ]
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->with('DELETE', 'https://dev-epoch.nft-investment.io/api/remote/v1/webhook/webhook123')
            ->willReturn(new Response(200, [], json_encode($expectedResponse)));
        
        // Act
        $result = $client->deleteWebhook('webhook123');
        
        // Assert
        $this->assertInstanceOf(WebhookDetailsResponseDTO::class, $result);
        $this->assertEquals(200, $result->code);
        $this->assertEquals('Webhook deleted', $result->message);
        $this->assertEquals('webhook123', $result->data->id);
        $this->assertEquals('https://example.com/webhook', $result->data->url);
    }

    public function test_create_payment_request_returns_create_payment_response_dto()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        $expectedResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'paymentLink' => [
                    'paymentId' => 'payment123',
                    'status' => 'pending'
                ]
            ]
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->with('POST', 'https://dev-epoch.nft-investment.io/api/remote/v1/payment-requests', $this->callback(function ($options) {
                return isset($options['json']['amount']) && $options['json']['amount'] === 100;
            }))
            ->willReturn(new Response(200, [], json_encode($expectedResponse)));
        
        // Act
        $result = $client->createPaymentRequest(['amount' => 100]);
        
        // Assert
        $this->assertInstanceOf(CreatePaymentResponseDTO::class, $result);
        $this->assertEquals(200, $result->code);
        $this->assertEquals('payment123', $result->data->paymentId);
        $this->assertEquals('pending', $result->data->status);
    }

    public function test_get_payment_request_returns_payment_details_response_dto()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        $expectedResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'payment' => [
                    'paymentId' => 'payment123',
                    'status' => 'completed'
                ]
            ]
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->with('GET', 'https://dev-epoch.nft-investment.io/api/remote/v1/payment-requests/payment123')
            ->willReturn(new Response(200, [], json_encode($expectedResponse)));
        
        // Act
        $result = $client->getPaymentRequest('payment123');
        
        // Assert
        $this->assertInstanceOf(PaymentDetailsResponseDTO::class, $result);
        $this->assertEquals(200, $result->code);
        $this->assertEquals('payment123', $result->data->paymentId);
        $this->assertEquals('completed', $result->data->status);
    }

    public function test_get_payment_gateway_returns_payment_gateway_response_dto()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        $expectedResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'gateway' => [
                    'gatewayId' => 'gateway123',
                    'name' => 'Test Gateway'
                ]
            ]
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->with('GET', 'https://dev-epoch.nft-investment.io/api/remote/v1/payment-gateways/gateway123')
            ->willReturn(new Response(200, [], json_encode($expectedResponse)));
        
        // Act
        $result = $client->getPaymentGateway('gateway123');
        
        // Assert
        $this->assertInstanceOf(PaymentGatewayResponseDTO::class, $result);
        $this->assertEquals(200, $result->code);
        $this->assertEquals('gateway123', $result->data->gatewayId);
        $this->assertEquals('Test Gateway', $result->data->name);
    }

    public function test_get_payment_methods_returns_payment_method_list_response_dto()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        $expectedResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'items' => [],
                'cursor' => null,
                'hasMore' => false
            ]
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->with('GET', 'https://dev-epoch.nft-investment.io/api/remote/v1/payment-method')
            ->willReturn(new Response(200, [], json_encode($expectedResponse)));
        
        // Act
        $result = $client->getPaymentMethods();
        
        // Assert
        $this->assertInstanceOf(PaymentMethodListResponseDTO::class, $result);
        $this->assertEquals(200, $result->code);
        $this->assertIsArray($result->data->items);
        $this->assertNull($result->data->cursor);
        $this->assertFalse($result->data->hasMore);
    }

    public function test_get_networks_returns_network_list_response_dto()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        $expectedResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'items' => [],
                'cursor' => null,
                'hasMore' => false
            ]
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->with('GET', 'https://dev-epoch.nft-investment.io/api/remote/v1/networks')
            ->willReturn(new Response(200, [], json_encode($expectedResponse)));
        
        // Act
        $result = $client->getNetworks();
        
        // Assert
        $this->assertInstanceOf(NetworkListResponseDTO::class, $result);
        $this->assertEquals(200, $result->code);
        $this->assertIsArray($result->data->items);
        $this->assertNull($result->data->cursor);
        $this->assertFalse($result->data->hasMore);
    }

    public function test_get_network_detail_returns_network_detail_response_dto()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        $expectedResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'network' => [
                    'chainId' => 1,
                    'name' => 'Ethereum'
                ]
            ]
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->with('POST', 'https://dev-epoch.nft-investment.io/api/remote/v1/network/detail', $this->callback(function ($options) {
                return isset($options['json']['chainId']) && $options['json']['chainId'] === 1;
            }))
            ->willReturn(new Response(200, [], json_encode($expectedResponse)));
        
        // Act
        $result = $client->getNetworkDetail(1);
        
        // Assert
        $this->assertInstanceOf(NetworkDetailResponseDTO::class, $result);
        $this->assertEquals(200, $result->code);
        $this->assertEquals(1, $result->data->chainId);
        $this->assertEquals('Ethereum', $result->data->name);
    }

    public function test_upload_file_to_ipfs_returns_ipfs_upload_response_dto()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        $expectedResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'cid' => 'QmTest123',
                'url' => 'https://ipfs.example.com/QmTest123'
            ]
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->with('POST', 'https://dev-epoch.nft-investment.io/api/remote/v1/ipfs/upload-file', $this->callback(function ($options) {
                return isset($options['multipart']) && is_array($options['multipart']);
            }))
            ->willReturn(new Response(200, [], json_encode($expectedResponse)));
        
        // Act
        $result = $client->uploadFileToIpfs('test-file');
        
        // Assert
        $this->assertInstanceOf(IpfsUploadResponseDTO::class, $result);
        $this->assertEquals(200, $result->code);
        $this->assertEquals('QmTest123', $result->data->cid);
        $this->assertEquals('https://ipfs.example.com/QmTest123', $result->data->url);
    }

    public function test_upload_folder_to_ipfs_returns_ipfs_folder_upload_response_dto()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        $expectedResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'cid' => 'QmFolder123',
                'url' => 'https://ipfs.example.com/QmFolder123'
            ]
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->with('POST', 'https://dev-epoch.nft-investment.io/api/remote/v1/ipfs/upload-files-to-folder', $this->callback(function ($options) {
                return isset($options['multipart']) && is_array($options['multipart']);
            }))
            ->willReturn(new Response(200, [], json_encode($expectedResponse)));
        
        // Act
        $result = $client->uploadFolderToIpfs([fopen('php://memory', 'rb')]);
        
        // Assert
        $this->assertInstanceOf(IpfsFolderUploadResponseDTO::class, $result);
        $this->assertEquals(200, $result->code);
        $this->assertEquals('QmFolder123', $result->data->cid);
        $this->assertEquals('https://ipfs.example.com/QmFolder123', $result->data->url);
    }

    public function test_upload_json_to_ipfs_returns_ipfs_upload_response_dto()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        $expectedResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'cid' => 'QmJson123',
                'url' => 'https://ipfs.example.com/QmJson123'
            ]
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->with('POST', 'https://dev-epoch.nft-investment.io/api/remote/v1/ipfs/upload-json', $this->callback(function ($options) {
                return isset($options['json']['json']) && $options['json']['json'] === json_encode(['key' => 'value']);
            }))
            ->willReturn(new Response(200, [], json_encode($expectedResponse)));
        
        // Act
        $result = $client->uploadJsonToIpfs(['key' => 'value']);
        
        // Assert
        $this->assertInstanceOf(IpfsUploadResponseDTO::class, $result);
        $this->assertEquals(200, $result->code);
        $this->assertEquals('QmJson123', $result->data->cid);
        $this->assertEquals('https://ipfs.example.com/QmJson123', $result->data->url);
    }

    public function test_get_ipfs_stats_returns_ipfs_stats_response_dto()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        $expectedResponse = [
            'code' => 200,
            'message' => 'Success',
            'user_stats' => [
                'total_uploads' => 100,
                'total_storage' => 1024000,
                'uploads_by_type' => [
                    'raw' => 50,
                    'json' => 30,
                    'folder' => 20,
                ],
            ]
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->with('GET', 'https://dev-epoch.nft-investment.io/api/remote/v1/ipfs/upload-stats')
            ->willReturn(new Response(200, [], json_encode($expectedResponse)));
        
        // Act
        $result = $client->getIpfsStats();
        
        // Assert
        $this->assertInstanceOf(IpfsStatsResponseDTO::class, $result);
        $this->assertEquals(200, $result->code);
        $this->assertEquals('Success', $result->message);
        $this->assertInstanceOf(\DVB\Core\SDK\DTOs\IpfsStatsDTO::class, $result->data);
        $this->assertEquals(100, $result->data->totalUploads);
        $this->assertEquals(1024000, $result->data->totalSize);
    }

    public function test_paginate_returns_pagination_iterator()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        // Act
        $result = $client->paginate('getNftsByContract', ['0x123', 1]);
        
        // Assert
        $this->assertInstanceOf(\DVB\Core\SDK\PaginationIterator::class, $result);
    }

    public function test_request_throws_exception_on_json_error()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        $httpClient->expects($this->once())
            ->method('request')
            ->willReturn(new Response(200, [], 'invalid json'));
        
        // Assert
        $this->expectException(DvbApiException::class);
        $this->expectExceptionMessage('Invalid JSON response');
        
        // Act
        $client->getProfile();
    }

}