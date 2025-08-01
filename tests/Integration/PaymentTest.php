<?php

namespace DVB\Core\SDK\Tests\Integration;

use DVB\Core\SDK\DTOs\PaymentMethodListResponseDTO;
use DVB\Core\SDK\DTOs\NetworkListResponseDTO;
use DVB\Core\SDK\Exceptions\DvbApiException;

class PaymentTest extends IntegrationTestCase
{
    public function test_get_payment_methods_returns_methods_list()
    {
        // Skip if integration tests are disabled
        if (!$this->isIntegrationTestEnabled()) {
            $this->markTestSkipped('Integration tests are disabled.');
        }

        $client = $this->getClient();
        
        try {
            $response = $client->getPaymentMethods();
            
            $this->assertInstanceOf(PaymentMethodListResponseDTO::class, $response);
            $this->assertEquals(200, $response->code);
            $this->assertIsString($response->message);
        } catch (DvbApiException $e) {
            // If we get a 401/403, it means the API key is invalid, which is expected in some test environments
            if (in_array($e->getCode(), [401, 403])) {
                $this->markTestSkipped('API key is invalid or missing required permissions.');
            }
            
            // Re-throw other exceptions
            throw $e;
        }
    }
    
    public function test_get_networks_returns_networks_list()
    {
        // Skip if integration tests are disabled
        if (!$this->isIntegrationTestEnabled()) {
            $this->markTestSkipped('Integration tests are disabled.');
        }

        $client = $this->getClient();
        
        try {
            $response = $client->getNetworks();
            
            $this->assertInstanceOf(NetworkListResponseDTO::class, $response);
            $this->assertEquals(200, $response->code);
            $this->assertIsString($response->message);
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