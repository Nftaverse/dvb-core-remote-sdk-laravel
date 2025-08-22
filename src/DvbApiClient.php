<?php

namespace DVB\Core\SDK;

use DVB\Core\SDK\Exceptions\ValidationException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\Http;
use JsonException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
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
use DVB\Core\SDK\DTOs\CollectionListResponseDTO;
use DVB\Core\SDK\DTOs\UserNftResponseDTO;

class DvbApiClient
{
    protected ClientInterface $httpClient;
    protected LoggerInterface $logger;
    protected string $apiKey;
    protected string $baseDomain;
    protected string $protocol;
    protected int $timeout;
    protected int $connectTimeout;
    protected bool $useLaravelHttp = false;

    /**
     * Create a new DvbApiClient instance.
     *
     * @param ClientInterface|null $httpClient
     * @param LoggerInterface|null $logger
     * @param string $apiKey
     * @param string $baseDomain
     * @param string $protocol
     * @param int $timeout
     * @param int $connectTimeout
     */
    public function __construct(
        ?ClientInterface $httpClient = null,
        ?LoggerInterface $logger = null,
        string $apiKey = '',
        string $baseDomain = 'dev-epoch.nft-investment.io',
        string $protocol = 'https',
        int $timeout = 30,
        int $connectTimeout = 10
    ) {
        $this->httpClient = $httpClient ?? new Client();
        $this->logger = $logger ?? new NullLogger();
        $this->setApiKey($apiKey);
        $this->setBaseDomain($baseDomain);
        $this->setProtocol($protocol);
        $this->timeout = $timeout;
        $this->connectTimeout = $connectTimeout;
    }

    /**
     * Create a new DvbApiClient instance with a specific API key and domain.
     *
     * @param string $apiKey
     * @param string $baseDomain
     * @param string $protocol
     * @param ClientInterface|null $httpClient
     * @param LoggerInterface|null $logger
     * @param int $timeout
     * @param int $connectTimeout
     * @return static
     */
    public static function newClient(
        string $apiKey,
        string $baseDomain,
        string $protocol = 'https',
        ?ClientInterface $httpClient = null,
        ?LoggerInterface $logger = null,
        int $timeout = 30,
        int $connectTimeout = 10
    ): self {
        return new static($httpClient, $logger, $apiKey, $baseDomain, $protocol, $timeout, $connectTimeout);
    }


    /**
     * Get the base URL for API requests.
     *
     * @return string
     */
    protected function getBaseUrl(): string
    {
        return "{$this->protocol}://{$this->baseDomain}/api/remote/v1";
    }

    /**
     * Make a GET request to the API.
     *
     * @param string $endpoint
     * @param array $query
     * @return array
     * @throws DvbApiException
     */
    protected function get(string $endpoint, array $query = []): array
    {
        return $this->request('GET', $endpoint, [
            'query' => $query,
        ]);
    }

    /**
     * Make a POST request to the API.
     *
     * @param string $endpoint
     * @param array $data
     * @param array $query
     * @return array
     * @throws DvbApiException
     */
    protected function post(string $endpoint, array $data = [], array $query = []): array
    {
        $options = ['json' => $data];
        if (!empty($query)) {
            $options['query'] = $query;
        }
        return $this->request('POST', $endpoint, $options);
    }

    /**
     * Make a PUT request to the API.
     *
     * @param string $endpoint
     * @param array $data
     * @return array
     * @throws DvbApiException
     */
    protected function put(string $endpoint, array $data = []): array
    {
        return $this->request('PUT', $endpoint, [
            'json' => $data,
        ]);
    }

    /**
     * Make a DELETE request to the API.
     *
     * @param string $endpoint
     * @return array
     * @throws DvbApiException
     */
    protected function delete(string $endpoint): array
    {
        return $this->request('DELETE', $endpoint);
    }

    /**
     * Make an HTTP request to the API.
     *
     * @param string $method
     * @param string $endpoint
     * @param array $options
     * @return array
     * @throws DvbApiException
     */
    protected function request(string $method, string $endpoint, array $options = []): array
    {
        try {
            $url = $this->getBaseUrl() . '/' . ltrim($endpoint, '/');
            
            // Prepare headers
            $headers = [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ];

            if (!isset($options['headers']['Content-Type'])) {
                $headers['Content-Type'] = 'application/json';
            }

            // Merge headers with existing options
            $options['headers'] = array_merge($options['headers'] ?? [], $headers);
            
            // Set timeout options
            $options['timeout'] = $this->timeout;
            $options['connect_timeout'] = $this->connectTimeout;

            $this->logger->info("Making {$method} request to {$url}", $options);

            // Use Laravel Http facade for Feature tests, GuzzleHttp for Unit tests
            if ($this->useLaravelHttp) {
                // Convert Guzzle-style options to Laravel Http style
                $laravelOptions = [
                    'headers' => $options['headers'] ?? [],
                    'timeout' => $options['timeout'],
                    'connect_timeout' => $options['connect_timeout'],
                ];

                // Handle different request types
                if (isset($options['json'])) {
                    $laravelOptions['json'] = $options['json'];
                }

                if (isset($options['query'])) {
                    $laravelOptions['query'] = $options['query'];
                }

                // Use Laravel Http client
                $http = Http::withOptions($laravelOptions);
                
                // Handle multipart requests
                if (isset($options['multipart'])) {
                    $http = $http->asMultipart();
                    foreach ($options['multipart'] as $part) {
                        $http = $http->attach($part['name'], $part['contents']);
                    }
                    $response = $http->{strtolower($method)}($url);
                } else {
                    $response = $http->{strtolower($method)}($url);
                }

                $body = $response->body();
                $statusCode = $response->status();
                
                // Handle error responses
                if ($statusCode >= 400) {
                    $this->logger->error("API request failed with status {$statusCode}: {$body}");
                    throw new DvbApiException("API request failed with status {$statusCode}: {$body}", $statusCode);
                }
            } else {
                $response = $this->httpClient->request($method, $url, $options);
                $body = $response->getBody()->getContents();
                $statusCode = $response->getStatusCode();
            }
            
            try {
                $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                throw new DvbApiException('Invalid JSON response from API', $statusCode, [], $e);
            }

            return $data ?: [];
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 422) {
                $errorBody = json_decode($e->getResponse()->getBody()->getContents(), true);
                $message = $errorBody['message'] ?? 'Validation failed';
                // Ensure message is a string
                if (is_array($message)) {
                    $message = json_encode($message);
                }
                throw new ValidationException(
                    $message,
                    $e->getCode(),
                    $errorBody['errors'] ?? [],
                    $e
                );
            }
            $this->logger->error("API Client Exception: " . $e->getMessage());
            throw new DvbApiException("API request failed: " . $e->getMessage(), $e->getCode(), [], $e);
        } catch (ServerException $e) {
            $this->logger->error("API Server Exception: " . $e->getMessage());
            throw new DvbApiException("API server error: " . $e->getMessage(), $e->getCode(), [], $e);
        } catch (GuzzleException $e) {
            $this->logger->error("API request failed: " . $e->getMessage());
            throw new DvbApiException("API request failed: " . $e->getMessage(), $e->getCode(), [], $e);
        }
    }

    /**
     * Get the HTTP client instance.
     *
     * @return \GuzzleHttp\ClientInterface
     */
    public function getHttpClient(): ClientInterface
    {
        return $this->httpClient;
    }

    /**
     * Get the logger instance.
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Get the API key.
     *
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * Get the base domain.
     *
     * @return string
     */
    public function getBaseDomain(): string
    {
        return $this->baseDomain;
    }

    /**
     * Get the protocol.
     *
     * @return string
     */
    public function getProtocol(): string
    {
        return $this->protocol;
    }

    /**
     * Set the API key.
     *
     * @param string $apiKey
     * @return $this
     */
    public function setApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * Set the base domain.
     *
     * @param string $baseDomain
     * @return $this
     */
    public function setBaseDomain(string $baseDomain): self
    {
        $this->baseDomain = $baseDomain;
        return $this;
    }

    /**
     * Set the protocol.
     *
     * @param string $protocol
     * @return $this
     */
    public function setProtocol(string $protocol): self
    {
        $this->protocol = $protocol;
        return $this;
    }

    /**
     * Enable Laravel Http facade for Feature tests.
     *
     * @return $this
     */
    public function useLaravelHttp(): self
    {
        $this->useLaravelHttp = true;
        return $this;
    }

    /**
     * Disable Laravel Http facade and use GuzzleHttp directly.
     *
     * @return $this
     */
    public function useGuzzleHttp(): self
    {
        $this->useLaravelHttp = false;
        return $this;
    }

    /**
     * Create a new user.
     *
     * @param string $email
     * @param string|null $name
     * @param string|null $phone
     * @return UserResponseDTO
     * @throws DvbApiException
     */
    public function createUser(string $email, ?string $name = null, ?string $phone = null): UserResponseDTO
    {
        $query = ['email' => $email];
        if ($name !== null) {
            $query['name'] = $name;
        }
        if ($phone !== null) {
            $query['phone'] = $phone;
        }
        $response = $this->post('user', [], $query);
        return UserResponseDTO::fromArray($response);
    }

    /**
     * Get a user by their identifier (UID, email, phone, or wallet address).
     *
     * @param string $identifier
     * @return UserResponseDTO
     * @throws DvbApiException
     */
    public function getUser(string $identifier): UserResponseDTO
    {
        $response = $this->get("user/{$identifier}");
        return UserResponseDTO::fromArray($response);
    }

    /**
     * Get NFTs owned by a user.
     *
     * @param string $uid
     * @param int $chainId
     * @param string|null $collectionAddress
     * @param string|null $cursor
     * @return UserNftResponseDTO
     * @throws DvbApiException
     */
    public function getNftsByUser(string $uid, int $chainId, ?string $collectionAddress = null, ?string $cursor = null): UserNftResponseDTO
    {
        $query = ['chain_id' => $chainId];
        if ($collectionAddress !== null) {
            $query['collection_address'] = $collectionAddress;
        }
        if ($cursor !== null) {
            $query['cursor'] = $cursor;
        }
        $response = $this->get("user/{$uid}/nft", $query);
        return UserNftResponseDTO::fromArray($response);
    }

    /**
     * Get user profile.
     *
     * @return \DVB\Core\SDK\DTOs\UserResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getProfile(): UserResponseDTO
    {
        $response = $this->get('profile');
        return UserResponseDTO::fromArray($response);
    }

    /**
     * Query user by field and value.
     *
     * @param string $field
     * @param string $value
     * @return \DVB\Core\SDK\DTOs\UserResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function queryUser(string $field, string $value): UserResponseDTO
    {
        $response = $this->post('query/user', [
            'field' => $field,
            'value' => $value,
        ]);
        return UserResponseDTO::fromArray($response);
    }

    /**
     * Get user permissions.
     *
     * @return \DVB\Core\SDK\DTOs\PermissionsResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getPermissions(): PermissionsResponseDTO
    {
        $response = $this->get('permission');
        return PermissionsResponseDTO::fromArray($response);
    }

    /**
     * Check if user has specific permissions.
     *
     * @param array|string $permission
     * @return \DVB\Core\SDK\DTOs\CheckPermissionResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function checkPermission(array|string $permission): CheckPermissionResponseDTO
    {
        $response = $this->post('permission/check', [
            'permission' => $permission,
        ]);
        return CheckPermissionResponseDTO::fromArray($response);
    }

    /**
     * Send email to user.
     *
     * @param string $email
     * @param string $subject
     * @param string $body
     * @return \DVB\Core\SDK\DTOs\ApiResponse
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function sendEmail(string $email, string $subject, string $body): ApiResponse
    {
        $response = $this->post('send-email', [
            'email' => $email,
            'subject' => $subject,
            'body' => $body,
        ]);
        return ApiResponse::fromArray($response);
    }

    /**
     * Send SMS to user.
     *
     * @param string $phone
     * @param string $body
     * @return \DVB\Core\SDK\DTOs\ApiResponse
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function sendSms(string $phone, string $body): ApiResponse
    {
        $response = $this->post('send-sms', [
            'phone' => $phone,
            'body' => $body,
        ]);
        return ApiResponse::fromArray($response);
    }

    /**
     * Get collections list.
     *
     * @param int $chainId
     * @param string|null $cursor
     * @return \DVB\Core\SDK\DTOs\CollectionListResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getCollections(int $chainId, ?string $cursor = null): CollectionListResponseDTO
    {
        $query = [
            'chain_id' => $chainId,
        ];
        
        if ($cursor) {
            $query['cursor'] = $cursor;
        }

        $response = $this->get('collection', $query);
        return CollectionListResponseDTO::fromArray($response);
    }

    /**
     * Get collection events.
     *
     * @param string $address
     * @param int $chainId
     * @return \DVB\Core\SDK\DTOs\CollectionEventListResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getCollectionEvents(string $address, int $chainId): CollectionEventListResponseDTO
    {
        $response = $this->get("collection/{$address}/event", [
            'chain_id' => $chainId,
        ]);
        return CollectionEventListResponseDTO::fromArray($response);
    }

    /**
     * Check collection.
     *
     * @param int $chainId
     * @param string $address
     * @param string $toAddress
     * @return \DVB\Core\SDK\DTOs\CheckCollectionResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function checkCollection(int $chainId, string $address, string $toAddress): CheckCollectionResponseDTO
    {
        $response = $this->post('collection/check', [], [
            'chain_id' => $chainId,
            'address' => $address,
            'to_address' => $toAddress,
        ]);
        return CheckCollectionResponseDTO::fromArray($response);
    }

    /**
     * Get NFTs by contract address.
     *
     * @param string $address
     * @param int $chainId
     * @param string|null $cursor
     * @return \DVB\Core\SDK\DTOs\NftListResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getNftsByContract(string $address, int $chainId, ?string $cursor = null): NftListResponseDTO
    {
        $query = [
            'chain_id' => $chainId,
        ];
        
        if ($cursor) {
            $query['cursor'] = $cursor;
        }

        $response = $this->get("nft/{$address}", $query);
        return NftListResponseDTO::fromArray($response);
    }

    /**
     * Get NFT metadata.
     *
     * @param string $address
     * @param string $tokenId
     * @param int $chainId
     * @return \DVB\Core\SDK\DTOs\NftMetadataResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getNftMetadata(string $address, string $tokenId, int $chainId): NftMetadataResponseDTO
    {
        $response = $this->get("nft/{$address}/{$tokenId}/metadata", [
            'chain_id' => $chainId,
        ]);
        return NftMetadataResponseDTO::fromArray($response);
    }

    /**
     * Get NFT job details.
     *
     * @param string $jobId
     * @return \DVB\Core\SDK\DTOs\NftJobDetailsResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getNftJobDetails(string $jobId): NftJobDetailsResponseDTO
    {
        $response = $this->get("nft/details/{$jobId}");
        return NftJobDetailsResponseDTO::fromArray($response);
    }

    /**
     * Create NFT event.
     *
     * @param array $data
     * @return \DVB\Core\SDK\DTOs\CheckCollectionResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function createNftEvent(array $data): CheckCollectionResponseDTO
    {
        $response = $this->post('nft/event', $data);
        return CheckCollectionResponseDTO::fromArray($response);
    }

    /**
     * Get webhooks.
     *
     * @return \DVB\Core\SDK\DTOs\WebhookListResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getWebhooks(): WebhookListResponseDTO
    {
        $response = $this->get('webhook');
        return WebhookListResponseDTO::fromArray($response);
    }

    /**
     * Create webhook.
     *
     * @param string $url
     * @param string $type
     * @param string|null $name
     * @param string|null $collectionAddress
     * @param string|null $collectionChainId
     * @return \DVB\Core\SDK\DTOs\WebhookListResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function createWebhook(string $url, string $type, ?string $name = null, ?string $collectionAddress = null, ?string $collectionChainId = null): WebhookListResponseDTO
    {
        $data = [
            'url' => $url,
            'type' => $type,
        ];

        if ($name) {
            $data['name'] = $name;
        }

        if ($collectionAddress) {
            $data['collectionAddress'] = $collectionAddress;
        }

        if ($collectionChainId) {
            $data['collectionChainId'] = $collectionChainId;
        }

        $response = $this->post('webhook', $data);
        return WebhookListResponseDTO::fromArray($response);
    }

    /**
     * Get webhook by ID.
     *
     * @param string $id
     * @return \DVB\Core\SDK\DTOs\WebhookDetailsResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getWebhook(string $id): WebhookDetailsResponseDTO
    {
        $response = $this->get("webhook/{$id}");
        return WebhookDetailsResponseDTO::fromArray($response);
    }

    /**
     * Delete webhook by ID.
     *
     * @param string $id
     * @return \DVB\Core\SDK\DTOs\WebhookDetailsResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function deleteWebhook(string $id): WebhookDetailsResponseDTO
    {
        $response = $this->delete("webhook/{$id}");
        return WebhookDetailsResponseDTO::fromArray($response);
    }

    /**
     * Create payment request.
     *
     * @param array $data
     * @return \DVB\Core\SDK\DTOs\CreatePaymentResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function createPaymentRequest(array $data): CreatePaymentResponseDTO
    {
        $response = $this->post('payment-requests', $data);
        return CreatePaymentResponseDTO::fromArray($response);
    }

    /**
     * Get payment request by ID.
     *
     * @param string $id
     * @return \DVB\Core\SDK\DTOs\PaymentDetailsResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getPaymentRequest(string $id): PaymentDetailsResponseDTO
    {
        $response = $this->get("payment-requests/{$id}");
        return PaymentDetailsResponseDTO::fromArray($response);
    }

    /**
     * Get payment gateway by ID.
     *
     * @param string $id
     * @return \DVB\Core\SDK\DTOs\PaymentGatewayResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getPaymentGateway(string $id): PaymentGatewayResponseDTO
    {
        $response = $this->get("payment-gateways/{$id}");
        return PaymentGatewayResponseDTO::fromArray($response);
    }

    /**
     * Get payment methods.
     *
     * @return \DVB\Core\SDK\DTOs\PaymentMethodListResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getPaymentMethods(): PaymentMethodListResponseDTO
    {
        $response = $this->get('payment-method');
        return PaymentMethodListResponseDTO::fromArray($response);
    }

    /**
     * Get networks.
     *
     * @return \DVB\Core\SDK\DTOs\NetworkListResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getNetworks(): NetworkListResponseDTO
    {
        $response = $this->get('networks');
        return NetworkListResponseDTO::fromArray($response);
    }

    /**
     * Get network detail.
     *
     * @param int $chainId
     * @return \DVB\Core\SDK\DTOs\NetworkDetailResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getNetworkDetail(int $chainId): NetworkDetailResponseDTO
    {
        $response = $this->post('network/detail', [
            'chainId' => $chainId,
        ]);
        return NetworkDetailResponseDTO::fromArray($response);
    }

    /**
     * Upload file to IPFS.
     *
     * @param resource $fileResource
     * @param bool $toCdn
     * @return IpfsUploadResponseDTO
     * @throws DvbApiException
     */
    public function uploadFileToIpfs($fileResource, bool $toCdn = true): IpfsUploadResponseDTO
    {
        $response = $this->request('POST', 'ipfs/upload-file', [
            'multipart' => [
                [
                    'name'     => 'file',
                    'contents' => $fileResource,
                ],
                [
                    'name'     => 'to_cdn',
                    'contents' => $toCdn ? 'true' : 'false',
                ],
            ],
        ]);
        return IpfsUploadResponseDTO::fromArray($response);
    }

    /**
     * Upload folder to IPFS.
     *
     * @param array $files An array of file resources.
     * @param bool $toCdn
     * @return IpfsFolderUploadResponseDTO
     * @throws DvbApiException
     */
    public function uploadFolderToIpfs(array $files, bool $toCdn = true): IpfsFolderUploadResponseDTO
    {
        $multipart = [];
        foreach ($files as $file) {
            $multipart[] = [
                'name'     => 'files[]',
                'contents' => $file,
            ];
        }

        $multipart[] = [
            'name'     => 'to_cdn',
            'contents' => $toCdn ? 'true' : 'false',
        ];

        $response = $this->request('POST', 'ipfs/upload-files-to-folder', [
            'multipart' => $multipart,
        ]);
        return IpfsFolderUploadResponseDTO::fromArray($response);
    }

    /**
     * Upload JSON to IPFS.
     *
     * @param array $jsonData
     * @param bool $toCdn
     * @return IpfsUploadResponseDTO
     * @throws DvbApiException
     */
    public function uploadJsonToIpfs(array $jsonData, bool $toCdn = true): IpfsUploadResponseDTO
    {
        $response = $this->post('ipfs/upload-json', [
            'json'   => $jsonData,
            'to_cdn' => $toCdn,
        ]);
        return IpfsUploadResponseDTO::fromArray($response);
    }

    /**
     * Get IPFS stats.
     *
     * @return IpfsStatsResponseDTO
     * @throws DvbApiException
     */
    public function getIpfsStats(): IpfsStatsResponseDTO
    {
        $response = $this->get('ipfs/upload-stats');
        return IpfsStatsResponseDTO::fromArray($response);
    }

    /**
     * Create a pagination iterator for a method.
     *
     * @param string $method
     * @param array $params
     * @return \DVB\Core\SDK\PaginationIterator
     */
    public function paginate(string $method, array $params = []): PaginationIterator
    {
        return new PaginationIterator($this, $method, $params);
    }
}