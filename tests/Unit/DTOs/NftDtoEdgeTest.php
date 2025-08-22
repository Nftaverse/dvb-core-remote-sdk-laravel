<?php

namespace DVB\Core\SDK\Tests\Unit\DTOs;

use DVB\Core\SDK\DTOs\NftDTO;
use DVB\Core\SDK\DTOs\NftAttributeDTO;
use DVB\Core\SDK\DTOs\NftMetadataDTO;
use DVB\Core\SDK\DTOs\NftListResponseDTO;
use DVB\Core\SDK\DTOs\NftMetadataResponseDTO;
use DVB\Core\SDK\DTOs\PaginatedNftDataDTO;
use DVB\Core\SDK\Tests\TestCase;

class NftDtoEdgeTest extends TestCase
{
    public function test_nft_dto_handles_unexpected_null_values_gracefully()
    {
        $data = [
            'tokenId' => null,
            'name' => null,
            'contractAddress' => null,
            'chainId' => null,
        ];

        $nft = NftDTO::fromArray($data);

        $this->assertInstanceOf(NftDTO::class, $nft);
        $this->assertEquals('', $nft->tokenId); // Should default to empty string
        $this->assertEquals('', $nft->name); // Should default to empty string
        $this->assertEquals('', $nft->contractAddress); // Should default to empty string
        $this->assertEquals(0, $nft->chainId); // Should default to 0
        $this->assertNull($nft->description);
        $this->assertNull($nft->image);
        $this->assertNull($nft->attributes);
    }

    public function test_nft_dto_ignores_extra_fields()
    {
        $data = [
            'tokenId' => '123',
            'name' => 'Test NFT',
            'contractAddress' => '0x123',
            'chainId' => 1,
            'extraField' => 'should be ignored',
            'anotherExtra' => ['a', 'b'],
        ];

        $nft = NftDTO::fromArray($data);

        $this->assertInstanceOf(NftDTO::class, $nft);
        $this->assertEquals('123', $nft->tokenId);
        $this->assertObjectNotHasProperty('extraField', $nft);
    }

    public function test_nft_attribute_dto_handles_mixed_value_types()
    {
        $data = [
            'trait_type' => 'Level',
            'value' => 10, // Numeric value
        ];

        $attribute = NftAttributeDTO::fromArray($data);
        $this->assertEquals(10, $attribute->value);

        $data = [
            'trait_type' => 'IsLegendary',
            'value' => true, // Boolean value
        ];
        $attribute = NftAttributeDTO::fromArray($data);
        $this->assertEquals(true, $attribute->value);
    }

    public function test_nft_list_response_dto_handles_malformed_data()
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'items' => "not_an_array", // Malformed items
                'cursor' => 'next_cursor',
                'hasMore' => true
            ],
        ];

        // It should handle this without crashing, though the items will be empty or null
        // depending on the fromArray implementation.
        // Assuming it casts to an empty array or handles the type error gracefully.
        $response = NftListResponseDTO::fromArray($data);

        $this->assertInstanceOf(PaginatedNftDataDTO::class, $response->data);
        $this->assertIsArray($response->data->items);
        $this->assertEmpty($response->data->items);
    }

    public function test_nft_metadata_response_dto_handles_missing_data_property()
    {
        $data = [
            'code' => 404,
            'message' => 'Not Found',
            // 'data' property is missing
        ];

        $response = NftMetadataResponseDTO::fromArray($data);

        $this->assertNull($response->data);
        $this->assertEquals(404, $response->code);
    }
}
