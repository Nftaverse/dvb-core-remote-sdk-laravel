<?php

namespace DVB\Core\SDK;

use DVB\Core\SDK\DTOs\Sso\GetOrCreateUuidResponseDTO;
use DVB\Core\SDK\DTOs\Sso\CheckUuidExistsResponseDTO;
use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;

class SsoApiClient extends SsoBaseClient
{
    /**
     * Create a new SsoApiClient instance.
     *
     * @param ClientInterface|null $httpClient
     * @param LoggerInterface|null $logger
     * @param string $apiToken
     * @param string $baseDomain
     * @param string $protocol
     * @param int $timeout
     * @param int $connectTimeout
     */
    public function __construct(
        ?ClientInterface $httpClient = null,
        ?LoggerInterface $logger = null,
        string $apiToken = '',
        string $baseDomain = 'sso.test',
        string $protocol = 'https',
        int $timeout = 30,
        int $connectTimeout = 10
    ) {
        parent::__construct($httpClient, $logger, $apiToken, $baseDomain, $protocol, $timeout, $connectTimeout);
    }

    /**
     * Create a new SsoApiClient instance with a specific API token and domain.
     *
     * @param string $apiToken
     * @param string $baseDomain
     * @param string $protocol
     * @param ClientInterface|null $httpClient
     * @param LoggerInterface|null $logger
     * @param int $timeout
     * @param int $connectTimeout
     * @return static
     */
    public static function newClient(
        string $apiToken,
        string $baseDomain = 'sso.test',
        string $protocol = 'https',
        ?ClientInterface $httpClient = null,
        ?LoggerInterface $logger = null,
        int $timeout = 30,
        int $connectTimeout = 10
    ): self {
        return new static($httpClient, $logger, $apiToken, $baseDomain, $protocol, $timeout, $connectTimeout);
    }

    /**
     * 根據電郵獲取或創建用戶的UUID.
     *
     * @param string $email
     * @return GetOrCreateUuidResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getOrCreateUuidByEmail(string $email): GetOrCreateUuidResponseDTO
    {
        $response = $this->post('internal/user/uuid', [
            'email' => $email,
        ]);
        return GetOrCreateUuidResponseDTO::fromArray($response);
    }

    /**
     * 檢查用戶UUID是否存在.
     *
     * @param string $uid
     * @return CheckUuidExistsResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function checkUuidExists(string $uid): CheckUuidExistsResponseDTO
    {
        $response = $this->get('internal/user/exists', [
            'uid' => $uid,
        ]);
        return CheckUuidExistsResponseDTO::fromArray($response);
    }
}