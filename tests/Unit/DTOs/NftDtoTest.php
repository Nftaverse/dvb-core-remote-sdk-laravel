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
}
