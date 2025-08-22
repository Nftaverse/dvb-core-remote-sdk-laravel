<?php

namespace DVB\Core\SDK\Tests\Unit\DTOs;

use DVB\Core\SDK\DTOs\PaymentMethodDTO;
use DVB\Core\SDK\DTOs\PaymentMethodListResponseDTO;
use DVB\Core\SDK\DTOs\NetworkDTO;
use DVB\Core\SDK\DTOs\NetworkListResponseDTO;
use DVB\Core\SDK\Tests\TestCase;

class PaymentDtoTest extends TestCase
{
    public function test_payment_method_dto_can_be_created_from_array()
    {
        $data = [
            'id' => 'pm123',
            'name' => 'Credit Card',
            'type' => 'card',
            'isDefault' => true,
            'createdAt' => '2023-01-01T00:00:00Z',
        ];

        $paymentMethod = PaymentMethodDTO::fromArray($data);

        $this->assertInstanceOf(PaymentMethodDTO::class, $paymentMethod);
        $this->assertEquals('pm123', $paymentMethod->id);
        $this->assertEquals('Credit Card', $paymentMethod->name);
        $this->assertEquals('card', $paymentMethod->type);
        $this->assertTrue($paymentMethod->is_default);
        $this->assertEquals('2023-01-01T00:00:00Z', $paymentMethod->createdAt);
    }

    public function test_payment_method_dto_can_be_created_with_missing_optional_fields()
    {
        $data = [
            'id' => 'pm123',
            'name' => 'Credit Card',
            'type' => 'card',
        ];

        $paymentMethod = PaymentMethodDTO::fromArray($data);

        $this->assertInstanceOf(PaymentMethodDTO::class, $paymentMethod);
        $this->assertEquals('pm123', $paymentMethod->id);
        $this->assertEquals('Credit Card', $paymentMethod->name);
        $this->assertEquals('card', $paymentMethod->type);
        $this->assertFalse($paymentMethod->is_default);
        $this->assertNull($paymentMethod->createdAt);
    }

    public function test_payment_method_dto_can_be_created_with_null_values()
    {
        $data = [
            'id' => 'pm123',
            'name' => 'Credit Card',
            'type' => 'card',
            'isDefault' => null,
            'createdAt' => null,
        ];

        $paymentMethod = PaymentMethodDTO::fromArray($data);

        $this->assertInstanceOf(PaymentMethodDTO::class, $paymentMethod);
        $this->assertEquals('pm123', $paymentMethod->id);
        $this->assertEquals('Credit Card', $paymentMethod->name);
        $this->assertEquals('card', $paymentMethod->type);
        $this->assertFalse($paymentMethod->is_default);
        $this->assertNull($paymentMethod->createdAt);
    }

    public function test_payment_method_list_response_dto_can_be_created_from_array()
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'items' => [
                    [
                        'id' => 'pm123',
                        'name' => 'Credit Card',
                        'type' => 'card',
                    ],
                    [
                        'id' => 'pm456',
                        'name' => 'PayPal',
                        'type' => 'paypal',
                    ]
                ],
                'cursor' => 'next_cursor',
                'hasMore' => true
            ],
        ];

        $response = PaymentMethodListResponseDTO::fromArray($data);

        $this->assertInstanceOf(PaymentMethodListResponseDTO::class, $response);
        $this->assertEquals(200, $response->code);
        $this->assertEquals('Success', $response->message);
        $this->assertIsArray($response->data->items);
        $this->assertCount(2, $response->data->items);
        $this->assertInstanceOf(PaymentMethodDTO::class, $response->data->items[0]);
        $this->assertEquals('pm123', $response->data->items[0]->id);
        $this->assertEquals('next_cursor', $response->data->cursor);
        $this->assertTrue($response->data->hasMore);
    }

    public function test_payment_method_list_response_dto_can_handle_empty_items()
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

        $response = PaymentMethodListResponseDTO::fromArray($data);

        $this->assertInstanceOf(PaymentMethodListResponseDTO::class, $response);
        $this->assertEquals(200, $response->code);
        $this->assertEquals('Success', $response->message);
        $this->assertIsArray($response->data->items);
        $this->assertCount(0, $response->data->items);
        $this->assertNull($response->data->cursor);
        $this->assertFalse($response->data->hasMore);
    }

    public function test_network_dto_can_be_created_from_array()
    {
        $data = [
            'chainId' => 1,
            'name' => 'Ethereum',
            'symbol' => 'ETH',
            'decimals' => 18,
            'rpcUrl' => 'https://ethereum.rpc',
            'explorerUrl' => 'https://etherscan.io',
        ];

        $network = NetworkDTO::fromArray($data);

        $this->assertInstanceOf(NetworkDTO::class, $network);
        $this->assertEquals(1, $network->chainId);
        $this->assertEquals('Ethereum', $network->name);
        $this->assertEquals('ETH', $network->symbol);
        $this->assertEquals(18, $network->decimals);
        $this->assertEquals('https://ethereum.rpc', $network->rpcUrl);
        $this->assertEquals('https://etherscan.io', $network->explorerUrl);
    }

    public function test_network_dto_can_be_created_with_missing_optional_fields()
    {
        $data = [
            'chainId' => 1,
            'name' => 'Ethereum',
        ];

        $network = NetworkDTO::fromArray($data);

        $this->assertInstanceOf(NetworkDTO::class, $network);
        $this->assertEquals(1, $network->chainId);
        $this->assertEquals('Ethereum', $network->name);
        $this->assertNull($network->symbol);
        $this->assertNull($network->decimals);
        $this->assertNull($network->rpcUrl);
        $this->assertNull($network->explorerUrl);
    }

    public function test_network_list_response_dto_can_be_created_from_array()
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'items' => [
                    [
                        'chainId' => 1,
                        'name' => 'Ethereum',
                    ],
                    [
                        'chainId' => 137,
                        'name' => 'Polygon',
                    ]
                ],
                'cursor' => 'next_cursor',
                'hasMore' => true
            ],
        ];

        $response = NetworkListResponseDTO::fromArray($data);

        $this->assertInstanceOf(NetworkListResponseDTO::class, $response);
        $this->assertEquals(200, $response->code);
        $this->assertEquals('Success', $response->message);
        $this->assertIsArray($response->data->items);
        $this->assertCount(2, $response->data->items);
        $this->assertInstanceOf(NetworkDTO::class, $response->data->items[0]);
        $this->assertEquals(1, $response->data->items[0]->chainId);
        $this->assertEquals('next_cursor', $response->data->cursor);
        $this->assertTrue($response->data->hasMore);
    }

    public function test_network_list_response_dto_can_handle_empty_items()
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

        $response = NetworkListResponseDTO::fromArray($data);

        $this->assertInstanceOf(NetworkListResponseDTO::class, $response);
        $this->assertEquals(200, $response->code);
        $this->assertEquals('Success', $response->message);
        $this->assertIsArray($response->data->items);
        $this->assertCount(0, $response->data->items);
        $this->assertNull($response->data->cursor);
        $this->assertFalse($response->data->hasMore);
    }
}