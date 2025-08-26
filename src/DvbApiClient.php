<?php

namespace DVB\Core\SDK;

use DVB\Core\SDK\Client\DvbBaseClient;
use DVB\Core\SDK\Client\UserClient;
use DVB\Core\SDK\Client\PermissionClient;
use DVB\Core\SDK\Client\CommunicationClient;
use DVB\Core\SDK\Client\CollectionClient;
use DVB\Core\SDK\Client\NftClient;
use DVB\Core\SDK\Client\WebhookClient;
use DVB\Core\SDK\Client\PaymentClient;
use DVB\Core\SDK\Client\NetworkClient;
use DVB\Core\SDK\Client\IpfsClient;
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
use DVB\Core\SDK\Enums\WebhookType;
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
use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;

class DvbApiClient extends DvbBaseClient
{
    /**
     * @var UserClient
     */
    protected UserClient $userClient;

    /**
     * @var PermissionClient
     */
    protected PermissionClient $permissionClient;

    /**
     * @var CommunicationClient
     */
    protected CommunicationClient $communicationClient;

    /**
     * @var CollectionClient
     */
    protected CollectionClient $collectionClient;

    /**
     * @var NftClient
     */
    protected NftClient $nftClient;

    /**
     * @var WebhookClient
     */
    protected WebhookClient $webhookClient;

    /**
     * @var PaymentClient
     */
    protected PaymentClient $paymentClient;

    /**
     * @var NetworkClient
     */
    protected NetworkClient $networkClient;

    /**
     * @var IpfsClient
     */
    protected IpfsClient $ipfsClient;

    /**
     * Enable Laravel Http facade for Feature tests.
     *
     * @return $this
     */
    public function useLaravelHttp(): self
    {
        parent::useLaravelHttp();
        
        // Also enable Laravel Http for all child clients
        $this->userClient->useLaravelHttp();
        $this->permissionClient->useLaravelHttp();
        $this->communicationClient->useLaravelHttp();
        $this->collectionClient->useLaravelHttp();
        $this->nftClient->useLaravelHttp();
        $this->webhookClient->useLaravelHttp();
        $this->paymentClient->useLaravelHttp();
        $this->networkClient->useLaravelHttp();
        $this->ipfsClient->useLaravelHttp();
        
        return $this;
    }

    /**
     * Disable Laravel Http facade and use GuzzleHttp directly.
     *
     * @return $this
     */
    public function useGuzzleHttp(): self
    {
        parent::useGuzzleHttp();
        
        // Also disable Laravel Http for all child clients
        $this->userClient->useGuzzleHttp();
        $this->permissionClient->useGuzzleHttp();
        $this->communicationClient->useGuzzleHttp();
        $this->collectionClient->useGuzzleHttp();
        $this->nftClient->useGuzzleHttp();
        $this->webhookClient->useGuzzleHttp();
        $this->paymentClient->useGuzzleHttp();
        $this->networkClient->useGuzzleHttp();
        $this->ipfsClient->useGuzzleHttp();
        
        return $this;
    }

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
        parent::__construct($httpClient, $logger, $apiKey, $baseDomain, $protocol, $timeout, $connectTimeout);
        
        // Initialize all clients
        $this->userClient = new UserClient($httpClient, $logger, $apiKey, $baseDomain, $protocol, $timeout, $connectTimeout);
        $this->permissionClient = new PermissionClient($httpClient, $logger, $apiKey, $baseDomain, $protocol, $timeout, $connectTimeout);
        $this->communicationClient = new CommunicationClient($httpClient, $logger, $apiKey, $baseDomain, $protocol, $timeout, $connectTimeout);
        $this->collectionClient = new CollectionClient($httpClient, $logger, $apiKey, $baseDomain, $protocol, $timeout, $connectTimeout);
        $this->nftClient = new NftClient($httpClient, $logger, $apiKey, $baseDomain, $protocol, $timeout, $connectTimeout);
        $this->webhookClient = new WebhookClient($httpClient, $logger, $apiKey, $baseDomain, $protocol, $timeout, $connectTimeout);
        $this->paymentClient = new PaymentClient($httpClient, $logger, $apiKey, $baseDomain, $protocol, $timeout, $connectTimeout);
        $this->networkClient = new NetworkClient($httpClient, $logger, $apiKey, $baseDomain, $protocol, $timeout, $connectTimeout);
        $this->ipfsClient = new IpfsClient($httpClient, $logger, $apiKey, $baseDomain, $protocol, $timeout, $connectTimeout);
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

    // User methods
    public function createUser(string $email, ?string $name = null, ?string $phone = null): UserResponseDTO
    {
        return $this->userClient->createUser($email, $name, $phone);
    }

    public function getUser(string $identifier): UserResponseDTO
    {
        return $this->userClient->getUser($identifier);
    }

    public function getNftsByUser(string $uid, int $chainId, ?string $collectionAddress = null, ?string $cursor = null): UserNftResponseDTO
    {
        return $this->userClient->getNftsByUser($uid, $chainId, $collectionAddress, $cursor);
    }

    public function getProfile(): UserResponseDTO
    {
        return $this->userClient->getProfile();
    }

    public function queryUser(string $field, string $value): UserResponseDTO
    {
        return $this->userClient->queryUser($field, $value);
    }

    // Permission methods
    public function getPermissions(): PermissionsResponseDTO
    {
        return $this->permissionClient->getPermissions();
    }

    public function checkPermission(array|string $permission): CheckPermissionResponseDTO
    {
        return $this->permissionClient->checkPermission($permission);
    }

    // Communication methods
    public function sendEmail(string $email, string $subject, string $body): ApiResponse
    {
        return $this->communicationClient->sendEmail($email, $subject, $body);
    }

    public function sendSms(string $phone, string $body): ApiResponse
    {
        return $this->communicationClient->sendSms($phone, $body);
    }

    // Collection methods
    public function getCollections(int $chainId, ?string $cursor = null): CollectionListResponseDTO
    {
        return $this->collectionClient->getCollections($chainId, $cursor);
    }

    public function getOwnCollections(?int $chainId = null, ?string $cursor = null): CollectionListResponseDTO
    {
        return $this->collectionClient->getOwnCollections($chainId, $cursor);
    }

    public function getCollectionEvents(string $address, int $chainId): CollectionEventListResponseDTO
    {
        return $this->collectionClient->getCollectionEvents($address, $chainId);
    }

    public function checkCollection(int $chainId, string $address, string $toAddress): CheckCollectionResponseDTO
    {
        return $this->collectionClient->checkCollection($chainId, $address, $toAddress);
    }

    // NFT methods
    public function getNftsByContract(string $address, int $chainId, ?string $cursor = null): NftListResponseDTO
    {
        return $this->nftClient->getNftsByContract($address, $chainId, $cursor);
    }

    public function getNftMetadata(string $address, string $tokenId, int $chainId): NftMetadataResponseDTO
    {
        return $this->nftClient->getNftMetadata($address, $tokenId, $chainId);
    }

    public function getNftJobDetails(string $jobId): NftJobDetailsResponseDTO
    {
        return $this->nftClient->getNftJobDetails($jobId);
    }

    public function createNftEvent(array $data): CheckCollectionResponseDTO
    {
        return $this->nftClient->createNftEvent($data);
    }

    // Webhook methods
    public function getWebhooks(): WebhookListResponseDTO
    {
        return $this->webhookClient->getWebhooks();
    }

    public function createWebhook(string $url, WebhookType $type, ?string $name = null, ?string $collectionAddress = null, ?string $collectionChainId = null): WebhookListResponseDTO
    {
        return $this->webhookClient->createWebhook($url, $type, $name, $collectionAddress, $collectionChainId);
    }

    public function getWebhook(string $id): WebhookDetailsResponseDTO
    {
        return $this->webhookClient->getWebhook($id);
    }

    public function deleteWebhook(string $id): WebhookDetailsResponseDTO
    {
        return $this->webhookClient->deleteWebhook($id);
    }

    // Payment methods
    public function createPaymentRequest(array $data): CreatePaymentResponseDTO
    {
        return $this->paymentClient->createPaymentRequest($data);
    }

    public function getPaymentRequest(string $id): PaymentDetailsResponseDTO
    {
        return $this->paymentClient->getPaymentRequest($id);
    }

    public function getPaymentGateway(string $id): PaymentGatewayResponseDTO
    {
        return $this->paymentClient->getPaymentGateway($id);
    }

    public function getPaymentMethods(): PaymentMethodListResponseDTO
    {
        return $this->paymentClient->getPaymentMethods();
    }

    // Network methods
    public function getNetworks(): NetworkListResponseDTO
    {
        return $this->networkClient->getNetworks();
    }

    public function getNetworkDetail(int $chainId): NetworkDetailResponseDTO
    {
        return $this->networkClient->getNetworkDetail($chainId);
    }

    // IPFS methods
    public function uploadFileToIpfs($fileResource, bool $toCdn = true): IpfsUploadResponseDTO
    {
        return $this->ipfsClient->uploadFileToIpfs($fileResource, $toCdn);
    }

    public function uploadFolderToIpfs(array $files, bool $toCdn = true): IpfsFolderUploadResponseDTO
    {
        return $this->ipfsClient->uploadFolderToIpfs($files, $toCdn);
    }

    public function uploadJsonToIpfs(array $jsonData, bool $toCdn = true): IpfsUploadResponseDTO
    {
        return $this->ipfsClient->uploadJsonToIpfs($jsonData, $toCdn);
    }

    public function getIpfsStats(): IpfsStatsResponseDTO
    {
        return $this->ipfsClient->getIpfsStats();
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