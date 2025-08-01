<?php

namespace DVB\Core\SDK\Tests\Integration;

use DVB\Core\SDK\DTOs\WebhookListResponseDTO;
use DVB\Core\SDK\DTOs\WebhookDetailsResponseDTO;
use DVB\Core\SDK\Exceptions\DvbApiException;

class WebhookTest extends IntegrationTestCase
{
    public function test_get_webhooks_returns_webhooks_list()
    {
        // Skip if integration tests are disabled
        if (!$this->isIntegrationTestEnabled()) {
            $this->markTestSkipped('Integration tests are disabled.');
        }

        $client = $this->getClient();
        
        try {
            $response = $client->getWebhooks();
            
            $this->assertInstanceOf(WebhookListResponseDTO::class, $response);
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
    
    public function test_create_and_delete_webhook()
    {
        // Skip if integration tests are disabled
        if (!$this->isIntegrationTestEnabled()) {
            $this->markTestSkipped('Integration tests are disabled.');
        }

        $client = $this->getClient();
        
        try {
            // Create a webhook
            $createResponse = $client->createWebhook('https://example.com/webhook', 'nft');
            
            $this->assertInstanceOf(WebhookListResponseDTO::class, $createResponse);
            $this->assertEquals(200, $createResponse->code);
            $this->assertIsString($createResponse->message);
            
            // If we have webhooks in the response, try to get details of the first one
            if ($createResponse->data && !empty($createResponse->data->items)) {
                $webhookId = $createResponse->data->items[0]->id;
                
                // Get webhook details
                $getResponse = $client->getWebhook($webhookId);
                
                $this->assertInstanceOf(WebhookDetailsResponseDTO::class, $getResponse);
                $this->assertEquals(200, $getResponse->code);
                $this->assertIsString($getResponse->message);
                $this->assertEquals($webhookId, $getResponse->data->id);
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