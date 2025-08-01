<?php

namespace DVB\Core\SDK;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
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

class DvbApiClient
{
    protected ClientInterface $httpClient;
    protected LoggerInterface $logger;
    protected string $apiKey;
    protected string $baseDomain;
    protected string $protocol;

    /**
     * Create a new DvbApiClient instance.
     *
     * @param \GuzzleHttp\ClientInterface|null $httpClient
     * @param \Psr\Log\LoggerInterface|null $logger
     * @param string $apiKey
     * @param string $baseDomain
     * @param string $protocol
     */
    public function __construct(
        ?ClientInterface $httpClient = null,
        ?LoggerInterface $logger = null,
        string $apiKey = '',
        string $baseDomain = 'api.dvb.com',
        string $protocol = 'https'
    ) {
        $this->httpClient = $httpClient ?? new Client();
        $this->logger = $logger ?? new NullLogger();
        $this->apiKey = $apiKey;
        $this->baseDomain = $baseDomain;
        $this->protocol = $protocol;
    }

    /**
     * Get the base URL for API requests.
     *
     * @return string
     */
    protected function getBaseUrl(): string
    {
        return "{$this->protocol}://{$this->baseDomain}";
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
     * @return array
     * @throws DvbApiException
     */
    protected function post(string $endpoint, array $data = []): array
    {
        return $this->request('POST', $endpoint, [
            'json' => $data,
        ]);
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
            
            $options = array_merge($options, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ],
            ]);

            $this->logger->info("Making {$method} request to {$url}", $options);

            $response = $this->httpClient->request($method, $url, $options);
            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new DvbApiException('Invalid JSON response: ' . json_last_error_msg());
            }

            return $data ?: [];
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
     * @param array $permissions
     * @return \DVB\Core\SDK\DTOs\CheckPermissionResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function checkPermission(array $permissions): CheckPermissionResponseDTO
    {
        $response = $this->post('permission/check', [
            'permissions' => $permissions,
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
            'chainId' => $chainId,
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
        $response = $this->post('collection/check', [
            'chainId' => $chainId,
            'address' => $address,
            'toAddress' => $toAddress,
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
            'chainId' => $chainId,
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
            'chainId' => $chainId,
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
     * @param mixed $fileResource
     * @param bool $toCdn
     * @return \DVB\Core\SDK\DTOs\IpfsUploadResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function uploadFileToIpfs($fileResource, bool $toCdn = true): IpfsUploadResponseDTO
    {
        $response = $this->post('ipfs/upload-file', [
            'file' => $fileResource,
            'toCdn' => $toCdn,
        ]);
        return IpfsUploadResponseDTO::fromArray($response);
    }

    /**
     * Upload folder to IPFS.
     *
     * @param array $files
     * @param bool $toCdn
     * @return \DVB\Core\SDK\DTOs\IpfsFolderUploadResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function uploadFolderToIpfs(array $files, bool $toCdn = true): IpfsFolderUploadResponseDTO
    {
        $response = $this->post('ipfs/upload-files-to-folder', [
            'files' => $files,
            'toCdn' => $toCdn,
        ]);
        return IpfsFolderUploadResponseDTO::fromArray($response);
    }

    /**
     * Upload JSON to IPFS.
     *
     * @param array $jsonData
     * @param bool $toCdn
     * @return \DVB\Core\SDK\DTOs\IpfsUploadResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function uploadJsonToIpfs(array $jsonData, bool $toCdn = true): IpfsUploadResponseDTO
    {
        $response = $this->post('ipfs/upload-json', [
            'jsonData' => $jsonData,
            'toCdn' => $toCdn,
        ]);
        return IpfsUploadResponseDTO::fromArray($response);
    }

    /**
     * Get IPFS stats.
     *
     * @return \DVB\Core\SDK\DTOs\IpfsStatsResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
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