<?php

namespace DVB\Core\SDK\Tests\Integration;

use DVB\Core\SDK\DTOs\PermissionsResponseDTO;
use DVB\Core\SDK\DTOs\CheckPermissionResponseDTO;
use DVB\Core\SDK\Exceptions\DvbApiException;

class PermissionTest extends IntegrationTestCase
{
    public function test_get_permissions_returns_permissions_data()
    {
        // Skip if integration tests are disabled
        if (!$this->isIntegrationTestEnabled()) {
            $this->markTestSkipped('Integration tests are disabled.');
        }

        $client = $this->getClient();
        
        try {
            $response = $client->getPermissions();
            
            $this->assertInstanceOf(PermissionsResponseDTO::class, $response);
            $this->assertEquals(200, $response->code);
            $this->assertIsString($response->message);
            
            if ($response->data) {
                $this->assertIsArray($response->data->permissions);
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
    
    public function test_check_permission_returns_permission_status()
    {
        // Skip if integration tests are disabled
        if (!$this->isIntegrationTestEnabled()) {
            $this->markTestSkipped('Integration tests are disabled.');
        }

        $client = $this->getClient();
        
        try {
            // Test with a common permission
            $response = $client->checkPermission(['read']);
            
            $this->assertInstanceOf(CheckPermissionResponseDTO::class, $response);
            $this->assertEquals(200, $response->code);
            $this->assertIsString($response->message);
            
            if ($response->data) {
                $this->assertIsBool($response->data->hasPermission);
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