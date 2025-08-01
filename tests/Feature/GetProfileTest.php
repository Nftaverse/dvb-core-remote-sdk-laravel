<?php

namespace DVB\Core\SDK\Tests\Feature;

use DVB\Core\SDK\DvbApiClient;
use DVB\Core\SDK\DTOs\UserResponseDTO;
use DVB\Core\SDK\Exceptions\DvbApiException;
use DVB\Core\SDK\Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class GetProfileTest extends TestCase
{
    public function test_it_can_get_user_profile_successfully()
    {
        // Arrange
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'code' => 200,
                'message' => 'Success',
                'data' => [
                    'uid' => 'user123',
                    'name' => 'John Doe',
                    'email' => 'john@example.com'
                ]
            ]))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $httpClient = new Client(['handler' => $handlerStack]);

        $client = new DvbApiClient($httpClient, null, 'test-key');

        // Act
        $response = $client->getProfile();

        // Assert
        $this->assertInstanceOf(UserResponseDTO::class, $response);
        $this->assertEquals(200, $response->code);
        $this->assertEquals('John Doe', $response->data->name);
    }

    public function test_it_throws_exception_on_api_error()
    {
        // Arrange
        $mock = new MockHandler([
            new Response(401, [], json_encode([
                'message' => 'Unauthorized',
            ]))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $httpClient = new Client(['handler' => $handlerStack]);

        $client = new DvbApiClient($httpClient, null, 'invalid-key');

        // Assert
        $this->expectException(DvbApiException::class);

        // Act
        $client->getProfile();
    }
}