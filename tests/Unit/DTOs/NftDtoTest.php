<?php

namespace DVB\Core\SDK\Tests\Unit\DTOs;

use DVB\Core\SDK\DTOs\NftDTO;
use DVB\Core\SDK\DTOs\NftAttributeDTO;
use DVB\Core\SDK\DTOs\NftMetadataDTO;
use DVB\Core\SDK\DTOs\NftListResponseDTO;
use DVB\Core\SDK\DTOs\NftMetadataResponseDTO;
use DVB\Core\SDK\DTOs\PaginatedNftDataDTO;
use DVB\Core\SDK\Tests\TestCase;

class NftDtoTest extends TestCase
{
    public function test_nft_dto_can_be_created_from_array()
    {
        $data = [
            'tokenId' => '123',
            'name' => 'Test NFT',
            'description' => 'Test Description',
            'image' => 'https://example.com/image.png',
            'contractAddress' => '0x123',
            'chainId' => 1,
            'attributes' => [
                [
                    'trait_type' => 'Color',
                    'value' => 'Blue',
                ],
                [
                    'trait_type' => 'Rarity',
                    'value' => 'Rare',
                ]
            ],
        ];

        $nft = NftDTO::fromArray($data);

        $this->assertInstanceOf(NftDTO::class, $nft);
        $this->assertEquals('123', $nft->tokenId);
        $this->assertEquals('Test NFT', $nft->name);
        $this->assertEquals('Test Description', $nft->description);
        $this->assertEquals('https://example.com/image.png', $nft->image);
        $this->assertEquals('0x123', $nft->contractAddress);
        $this->assertEquals(1, $nft->chainId);
        $this->assertIsArray($nft->attributes);
        $this->assertCount(2, $nft->attributes);
        $this->assertInstanceOf(NftAttributeDTO::class, $nft->attributes[0]);
        $this->assertEquals('Color', $nft->attributes[0]->traitType);
        $this->assertEquals('Blue', $nft->attributes[0]->value);
    }

    public function test_nft_dto_can_be_created_with_missing_optional_fields()
    {
        $data = [
            'tokenId' => '123',
            'name' => 'Test NFT',
            'contractAddress' => '0x123',
            'chainId' => 1,
        ];

        $nft = NftDTO::fromArray($data);

        $this->assertInstanceOf(NftDTO::class, $nft);
        $this->assertEquals('123', $nft->tokenId);
        $this->assertEquals('Test NFT', $nft->name);
        $this->assertNull($nft->description);
        $this->assertNull($nft->image);
        $this->assertEquals('0x123', $nft->contractAddress);
        $this->assertEquals(1, $nft->chainId);
        $this->assertNull($nft->attributes);
    }

    public function test_nft_dto_can_be_created_with_null_values()
    {
        $data = [
            'tokenId' => '123',
            'name' => 'Test NFT',
            'description' => null,
            'image' => null,
            'contractAddress' => '0x123',
            'chainId' => 1,
            'attributes' => null,
        ];

        $nft = NftDTO::fromArray($data);

        $this->assertInstanceOf(NftDTO::class, $nft);
        $this->assertEquals('123', $nft->tokenId);
        $this->assertEquals('Test NFT', $nft->name);
        $this->assertNull($nft->description);
        $this->assertNull($nft->image);
        $this->assertEquals('0x123', $nft->contractAddress);
        $this->assertEquals(1, $nft->chainId);
        $this->assertNull($nft->attributes);
    }

    public function test_nft_attribute_dto_can_be_created_from_array()
    {
        $data = [
            'trait_type' => 'Color',
            'value' => 'Blue',
            'display_type' => 'string',
        ];

        $attribute = NftAttributeDTO::fromArray($data);

        $this->assertInstanceOf(NftAttributeDTO::class, $attribute);
        $this->assertEquals('Color', $attribute->traitType);
        $this->assertEquals('Blue', $attribute->value);
        $this->assertEquals('string', $attribute->displayType);
    }

    public function test_nft_attribute_dto_can_be_created_with_missing_fields()
    {
        $data = [
            'trait_type' => 'Color',
            'value' => 'Blue',
        ];

        $attribute = NftAttributeDTO::fromArray($data);

        $this->assertInstanceOf(NftAttributeDTO::class, $attribute);
        $this->assertEquals('Color', $attribute->traitType);
        $this->assertEquals('Blue', $attribute->value);
        $this->assertNull($attribute->displayType);
    }

    public function test_nft_metadata_dto_can_be_created_from_array()
    {
        $data = [
            'name' => 'Test NFT',
            'description' => 'Test Description',
            'image' => 'https://example.com/image.png',
            'attributes' => [
                [
                    'trait_type' => 'Color',
                    'value' => 'Blue',
                ]
            ],
        ];

        $metadata = NftMetadataDTO::fromArray($data);

        $this->assertInstanceOf(NftMetadataDTO::class, $metadata);
        $this->assertEquals('Test NFT', $metadata->name);
        $this->assertEquals('Test Description', $metadata->description);
        $this->assertEquals('https://example.com/image.png', $metadata->image);
        $this->assertIsArray($metadata->attributes);
        $this->assertCount(1, $metadata->attributes);
        $this->assertInstanceOf(NftAttributeDTO::class, $metadata->attributes[0]);
        $this->assertNull($metadata->expiresAt);
        $this->assertNull($metadata->additionalData);
    }

    public function test_nft_metadata_dto_can_be_created_with_expires_at()
    {
        $timestamp = time();
        $data = [
            'name' => 'Test NFT',
            'description' => 'Test Description',
            'image' => 'https://example.com/image.png',
            'expires_at' => $timestamp,
        ];

        $metadata = NftMetadataDTO::fromArray($data);

        $this->assertInstanceOf(NftMetadataDTO::class, $metadata);
        $this->assertEquals('Test NFT', $metadata->name);
        $this->assertEquals('Test Description', $metadata->description);
        $this->assertEquals('https://example.com/image.png', $metadata->image);
        $this->assertEquals($timestamp, $metadata->expiresAt);
        $this->assertNull($metadata->additionalData);
    }

    public function test_nft_metadata_dto_can_be_created_with_additional_data()
    {
        $data = [
            'name' => 'Test NFT',
            'description' => 'Test Description',
            'image' => 'https://example.com/image.png',
            'custom_field' => 'custom_value',
            'custom_number' => 123,
            'custom_array' => ['item1', 'item2'],
        ];

        $metadata = NftMetadataDTO::fromArray($data);

        $this->assertInstanceOf(NftMetadataDTO::class, $metadata);
        $this->assertEquals('Test NFT', $metadata->name);
        $this->assertEquals('Test Description', $metadata->description);
        $this->assertEquals('https://example.com/image.png', $metadata->image);
        $this->assertNull($metadata->expiresAt);
        $this->assertIsArray($metadata->additionalData);
        $this->assertEquals('custom_value', $metadata->additionalData['custom_field']);
        $this->assertEquals(123, $metadata->additionalData['custom_number']);
        $this->assertEquals(['item1', 'item2'], $metadata->additionalData['custom_array']);
    }

    public function test_nft_metadata_dto_additional_data_does_not_override_existing_fields()
    {
        $data = [
            'name' => 'Test NFT',
            'description' => 'Test Description',
            'image' => 'https://example.com/image.png',
            // 這些字段應該被忽略，因為它們是保留字段
            'external_url' => 'https://example.com',
            'custom_field' => 'custom_value',
        ];

        $metadata = NftMetadataDTO::fromArray($data);

        $this->assertInstanceOf(NftMetadataDTO::class, $metadata);
        $this->assertEquals('Test NFT', $metadata->name);
        $this->assertEquals('Test Description', $metadata->description);
        $this->assertEquals('https://example.com/image.png', $metadata->image);
        $this->assertEquals('https://example.com', $metadata->externalUrl);
        $this->assertIsArray($metadata->additionalData);
        $this->assertEquals('custom_value', $metadata->additionalData['custom_field']);
        // 確保 external_url 不在 additionalData 中
        $this->assertArrayNotHasKey('external_url', $metadata->additionalData);
    }

    public function test_nft_list_response_dto_can_be_created_from_array()
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'items' => [
                    [
                        'tokenId' => '123',
                        'name' => 'Test NFT 1',
                        'contractAddress' => '0x123',
                        'chainId' => 1,
                    ],
                    [
                        'tokenId' => '456',
                        'name' => 'Test NFT 2',
                        'contractAddress' => '0x123',
                        'chainId' => 1,
                    ]
                ],
                'cursor' => 'next_cursor',
                'hasMore' => true
            ],
        ];

        $response = NftListResponseDTO::fromArray($data);

        $this->assertInstanceOf(NftListResponseDTO::class, $response);
        $this->assertEquals(200, $response->code);
        $this->assertEquals('Success', $response->message);
        $this->assertInstanceOf(PaginatedNftDataDTO::class, $response->data);
        $this->assertIsArray($response->data->items);
        $this->assertCount(2, $response->data->items);
        $this->assertInstanceOf(NftDTO::class, $response->data->items[0]);
        $this->assertEquals('next_cursor', $response->data->cursor);
        $this->assertTrue($response->data->hasMore);
    }

    public function test_nft_list_response_dto_can_handle_empty_items()
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

        $response = NftListResponseDTO::fromArray($data);

        $this->assertInstanceOf(NftListResponseDTO::class, $response);
        $this->assertEquals(200, $response->code);
        $this->assertEquals('Success', $response->message);
        $this->assertInstanceOf(PaginatedNftDataDTO::class, $response->data);
        $this->assertIsArray($response->data->items);
        $this->assertCount(0, $response->data->items);
        $this->assertNull($response->data->cursor);
        $this->assertFalse($response->data->hasMore);
    }

    public function test_nft_metadata_response_dto_can_be_created_from_array()
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'name' => 'Test NFT',
                'description' => 'Test Description',
                'image' => 'https://example.com/image.png',
            ],
        ];

        $response = NftMetadataResponseDTO::fromArray($data);

        $this->assertInstanceOf(NftMetadataResponseDTO::class, $response);
        $this->assertEquals(200, $response->code);
        $this->assertEquals('Success', $response->message);
        $this->assertInstanceOf(NftMetadataDTO::class, $response->data);
        $this->assertEquals('Test NFT', $response->data->name);
    }

    public function test_nft_metadata_response_dto_can_handle_null_data()
    {
        $data = [
            'code' => 404,
            'message' => 'NFT metadata not found',
            'data' => null,
        ];

        $response = NftMetadataResponseDTO::fromArray($data);

        $this->assertInstanceOf(NftMetadataResponseDTO::class, $response);
        $this->assertEquals(404, $response->code);
        $this->assertEquals('NFT metadata not found', $response->message);
        $this->assertNull($response->data);
    }

    public function test_nft_metadata_response_dto_with_various_nft_formats()
    {
        // 測試不同的 NFT 元數據格式
        $testCases = [
            // 基本格式
            [
                'code' => 200,
                'message' => 'Success',
                'data' => [
                    'name' => 'Basic NFT',
                    'description' => 'Basic description',
                    'image' => 'https://example.com/image.png',
                ],
            ],
            // 包含 expires_at 的格式
            [
                'code' => 200,
                'message' => 'Success',
                'data' => [
                    'name' => 'NFT with expiration',
                    'description' => 'Description with expiration',
                    'image' => 'https://example.com/image2.png',
                    'expires_at' => 1694448000,
                ],
            ],
            // 包含自定義字段的格式
            [
                'code' => 200,
                'message' => 'Success',
                'data' => [
                    'name' => 'NFT with custom fields',
                    'description' => 'Description with custom fields',
                    'image' => 'https://example.com/image3.png',
                    'custom_field1' => 'value1',
                    'custom_field2' => ['nested', 'array'],
                    'properties' => [
                        'rarity' => 'legendary',
                        'level' => 100,
                    ],
                ],
            ],
            // OpenSea 格式示例
            [
                'code' => 200,
                'message' => 'Success',
                'data' => [
                    'name' => 'OpenSea Style NFT',
                    'description' => 'OpenSea style description',
                    'image' => 'https://example.com/opensea-image.png',
                    'external_url' => 'https://opensea.io/assets/123',
                    'animation_url' => 'https://example.com/animation.mp4',
                    'background_color' => 'ffffff',
                    'youtube_url' => 'https://www.youtube.com/watch?v=123',
                    'attributes' => [
                        [
                            'trait_type' => 'Rarity',
                            'value' => 'Legendary',
                        ],
                        [
                            'trait_type' => 'Level',
                            'value' => 100,
                        ],
                    ],
                    // 額外的 OpenSea 字段
                    'compiler' => 'HashLips Art Engine',
                    'collection' => 'My Collection',
                ],
            ],
        ];

        foreach ($testCases as $index => $testCase) {
            $response = NftMetadataResponseDTO::fromArray($testCase);

            $this->assertInstanceOf(NftMetadataResponseDTO::class, $response, "Test case {$index} failed");
            $this->assertEquals($testCase['code'], $response->code, "Test case {$index} code mismatch");
            $this->assertEquals($testCase['message'], $response->message, "Test case {$index} message mismatch");
            $this->assertInstanceOf(NftMetadataDTO::class, $response->data, "Test case {$index} data type mismatch");

            // 驗證基本字段
            $this->assertEquals($testCase['data']['name'], $response->data->name, "Test case {$index} name mismatch");
            $this->assertEquals($testCase['data']['description'], $response->data->description, "Test case {$index} description mismatch");
            $this->assertEquals($testCase['data']['image'], $response->data->image, "Test case {$index} image mismatch");

            // 驗證可選字段
            if (isset($testCase['data']['expires_at'])) {
                $this->assertEquals($testCase['data']['expires_at'], $response->data->expiresAt, "Test case {$index} expiresAt mismatch");
            }

            // 驗證額外數據字段
            if (isset($testCase['data']['custom_field1'])) {
                $this->assertIsArray($response->data->additionalData, "Test case {$index} additionalData should be array");
                $this->assertEquals($testCase['data']['custom_field1'], $response->data->additionalData['custom_field1'], "Test case {$index} custom_field1 mismatch");
            }

            if (isset($testCase['data']['compiler'])) {
                $this->assertIsArray($response->data->additionalData, "Test case {$index} additionalData should be array");
                $this->assertEquals($testCase['data']['compiler'], $response->data->additionalData['compiler'], "Test case {$index} compiler mismatch");
                // 確保保留字段不會出現在 additionalData 中
                $this->assertArrayNotHasKey('external_url', $response->data->additionalData, "Test case {$index} external_url should not be in additionalData");
            }
        }
    }
}
