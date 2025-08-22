<?php

namespace DVB\Core\SDK\Tests\Unit;

use DVB\Core\SDK\DvbApiClient;
use DVB\Core\SDK\Exceptions\ValidationException;
use DVB\Core\SDK\Tests\TestCase;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class DvbApiClientErrorHandlingTest extends TestCase
{
    public function test_it_handles_422_validation_exception()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The given data was invalid.');

        $httpClient = $this->createMock(\GuzzleHttp\ClientInterface::class);
        $client = new DvbApiClient($httpClient, null, 'test-key');

        $request = new Request('POST', 'https://dev-epoch.nft-investment.io/some-endpoint');
        $responseBody = json_encode([
            'message' => 'The given data was invalid.',
            'errors' => [
                'field' => ['The field is required.'],
            ],
        ]);
        $response = new Response(422, [], $responseBody);

        $httpClient->method('request')
            ->willThrowException(new \GuzzleHttp\Exception\ClientException('Error', $request, $response));

        try {
            $client->getProfile();
        } catch (ValidationException $e) {
            $this->assertEquals(['field' => ['The field is required.']], $e->getErrors());
            throw $e;
        }
    }

    public function test_it_handles_server_exception()
    {
        $this->expectException(\DVB\Core\SDK\Exceptions\DvbApiException::class);
        $this->expectExceptionCode(500);

        $httpClient = $this->createMock(\GuzzleHttp\ClientInterface::class);
        $client = new DvbApiClient($httpClient, null, 'test-key');

        $request = new Request('GET', 'https://dev-epoch.nft-investment.io/profile');
        $response = new Response(500, [], 'Internal Server Error');

        $httpClient->method('request')
            ->willThrowException(new \GuzzleHttp\Exception\ServerException('Error', $request, $response));
        
        $client->getProfile();
    }
}
