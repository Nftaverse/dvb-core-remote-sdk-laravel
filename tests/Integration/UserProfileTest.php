<?php

namespace DVB\Core\SDK\Tests\Integration;

use DVB\Core\SDK\DTOs\UserResponseDTO;
use DVB\Core\SDK\Exceptions\DvbApiException;

class UserProfileTest extends IntegrationTestCase
{
    public function test_get_profile_returns_user_data()
    {
        // Skip if integration tests are disabled
        if (!$this->isIntegrationTestEnabled()) {
            $this->markTestSkipped('Integration tests are disabled.');
        }

        $client = $this->getClient();
        
        try {
            $response = $client->getProfile();
            
            $this->assertInstanceOf(UserResponseDTO::class, $response);
            $this->assertEquals(200, $response->code);
            $this->assertIsString($response->message);
            
            if ($response->data) {
                $this->assertIsString($response->data->uid);
                $this->assertIsString($response->data->name);
                $this->assertIsString($response->data->email);
            }
        } catch (DvbApiException $e) {
            // If we get a 401/403, it means the API key is invalid, which is expected in some test environments
            if (in_array($e->getCode(), [401, 403])) {
                $this->markTestSkipped('API key is invalid or missing required permissions.');
            }
            
            // Re-throw other exceptions
            throw $e;
        }
    }
}