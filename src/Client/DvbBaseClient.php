<?php

namespace DVB\Core\SDK\Client;

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

class DvbBaseClient
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
     * Create a new DvbBaseClient instance.
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
     * Create a new DvbBaseClient instance with a specific API key and domain.
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

            // 設置適當的 Content-Type
            if (!isset($options['headers']['Content-Type'])) {
                if (isset($options['form_params'])) {
                    $headers['Content-Type'] = 'application/x-www-form-urlencoded';
                } else if (isset($options['multipart'])) {
                    // 對於 multipart 請求，讓 Guzzle 自動設置 Content-Type
                    // 不要手動設置，因為 Guzzle 會添加 boundary 參數
                } else {
                    $headers['Content-Type'] = 'application/json';
                }
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
}