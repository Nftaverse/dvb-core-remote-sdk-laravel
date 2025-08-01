<?php

namespace DVB\Core\SDK\Tests\Unit\DTOs;

use DVB\Core\SDK\DTOs\WebhookDTO;
use DVB\Core\SDK\DTOs\WebhookListResponseDTO;
use DVB\Core\SDK\DTOs\WebhookDetailsResponseDTO;
use DVB\Core\SDK\Tests\TestCase;

class WebhookDtoTest extends TestCase
{
    public function test_webhook_dto_can_be_created_from_array()
    {
        $data = [
            'id' => 'webhook123',
            'url' => 'https://example.com/webhook',
            'type' => 'nft',
            'name' => 'Test Webhook',
            'createdAt' => '2023-01-01T00:00:00Z',
            'updatedAt' => '2023-01-02T00:00:00Z',
        ];

        $webhook = WebhookDTO::fromArray($data);

        $this->assertInstanceOf(WebhookDTO::class, $webhook);
        $this->assertEquals('webhook123', $webhook->id);
        $this->assertEquals('https://example.com/webhook', $webhook->url);
        $this->assertEquals('nft', $webhook->type);
        $this->assertEquals('Test Webhook', $webhook->name);
        $this->assertEquals('2023-01-01T00:00:00Z', $webhook->createdAt);
        $this->assertEquals('2023-01-02T00:00:00Z', $webhook->updatedAt);
    }

    public function test_webhook_dto_can_be_created_with_missing_optional_fields()
    {
        $data = [
            'id' => 'webhook123',
            'url' => 'https://example.com/webhook',
            'type' => 'nft',
        ];

        $webhook = WebhookDTO::fromArray($data);

        $this->assertInstanceOf(WebhookDTO::class, $webhook);
        $this->assertEquals('webhook123', $webhook->id);
        $this->assertEquals('https://example.com/webhook', $webhook->url);
        $this->assertEquals('nft', $webhook->type);
        $this->assertNull($webhook->name);
        $this->assertNull($webhook->createdAt);
        $this->assertNull($webhook->updatedAt);
    }

    public function test_webhook_dto_can_be_created_with_null_values()
    {
        $data = [
            'id' => 'webhook123',
            'url' => 'https://example.com/webhook',
            'type' => 'nft',
            'name' => null,
            'createdAt' => null,
            'updatedAt' => null,
        ];

        $webhook = WebhookDTO::fromArray($data);

        $this->assertInstanceOf(WebhookDTO::class, $webhook);
        $this->assertEquals('webhook123', $webhook->id);
        $this->assertEquals('https://example.com/webhook', $webhook->url);
        $this->assertEquals('nft', $webhook->type);
        $this->assertNull($webhook->name);
        $this->assertNull($webhook->createdAt);
        $this->assertNull($webhook->updatedAt);
    }

    public function test_webhook_list_response_dto_can_be_created_from_array()
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'items' => [
                    [
                        'id' => 'webhook123',
                        'url' => 'https://example.com/webhook1',
                        'type' => 'nft',
                    ],
                    [
                        'id' => 'webhook456',
                        'url' => 'https://example.com/webhook2',
                        'type' => 'collection',
                    ]
                ],
                'cursor' => 'next_cursor',
                'hasMore' => true
            ],
        ];

        $response = WebhookListResponseDTO::fromArray($data);

        $this->assertInstanceOf(WebhookListResponseDTO::class, $response);
        $this->assertEquals(200, $response->code);
        $this->assertEquals('Success', $response->message);
        $this->assertIsArray($response->data->items);
        $this->assertCount(2, $response->data->items);
        $this->assertInstanceOf(WebhookDTO::class, $response->data->items[0]);
        $this->assertEquals('webhook123', $response->data->items[0]->id);
        $this->assertEquals('next_cursor', $response->data->cursor);
        $this->assertTrue($response->data->hasMore);
    }

    public function test_webhook_list_response_dto_can_handle_empty_items()
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'items' => [],
                'cursor' => null,
                'hasMore' => false
            ],
        ];

        $response = WebhookListResponseDTO::fromArray($data);

        $this->assertInstanceOf(WebhookListResponseDTO::class, $response);
        $this->assertEquals(200, $response->code);
        $this->assertEquals('Success', $response->message);
        $this->assertIsArray($response->data->items);
        $this->assertCount(0, $response->data->items);
        $this->assertNull($response->data->cursor);
        $this->assertFalse($response->data->hasMore);
    }

    public function test_webhook_details_response_dto_can_be_created_from_array()
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'id' => 'webhook123',
                'url' => 'https://example.com/webhook',
                'type' => 'nft',
                'name' => 'Test Webhook',
            ],
        ];

        $response = WebhookDetailsResponseDTO::fromArray($data);

        $this->assertInstanceOf(WebhookDetailsResponseDTO::class, $response);
        $this->assertEquals(200, $response->code);
        $this->assertEquals('Success', $response->message);
        $this->assertInstanceOf(WebhookDTO::class, $response->data);
        $this->assertEquals('webhook123', $response->data->id);
        $this->assertEquals('https://example.com/webhook', $response->data->url);
    }

    public function test_webhook_details_response_dto_can_handle_null_data()
    {
        $data = [
            'code' => 404,
            'message' => 'Webhook not found',
            'data' => null,
        ];

        $response = WebhookDetailsResponseDTO::fromArray($data);

        $this->assertInstanceOf(WebhookDetailsResponseDTO::class, $response);
        $this->assertEquals(404, $response->code);
        $this->assertEquals('Webhook not found', $response->message);
        $this->assertNull($response->data);
    }
}