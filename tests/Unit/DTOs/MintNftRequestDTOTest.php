<?php

namespace DVB\Core\SDK\Tests\Unit\DTOs;

use DVB\Core\SDK\DTOs\MintNftRequestDTO;
use DVB\Core\SDK\Tests\TestCase;
use InvalidArgumentException;

class MintNftRequestDTOTest extends TestCase
{
    public function test_constructor_with_valid_metadata(): void
    {
        // Arrange
        $validMetadata = json_encode([
            [
                'name' => 'Test NFT',
                'description' => 'A test NFT',
                'image' => 'https://example.com/image.png'
            ]
        ]);

        // Act
        $dto = new MintNftRequestDTO(
            chainId: 1,
            address: '0x123',
            toAddress: '0x456',
            amount: 1,
            metadata: $validMetadata
        );

        // Assert
        $this->assertEquals($validMetadata, $dto->metadata);
    }

    public function test_constructor_with_valid_metadata_with_attributes(): void
    {
        // Arrange
        $validMetadata = json_encode([
            [
                'name' => 'Test NFT',
                'description' => 'A test NFT',
                'image' => 'https://example.com/image.png',
                'attributes' => [
                    [
                        'trait_type' => 'Rarity',
                        'value' => 'Common'
                    ],
                    [
                        'trait_type' => 'Level',
                        'value' => 1
                    ]
                ]
            ]
        ]);

        // Act
        $dto = new MintNftRequestDTO(
            chainId: 1,
            address: '0x123',
            toAddress: '0x456',
            amount: 1,
            metadata: $validMetadata
        );

        // Assert
        $this->assertEquals($validMetadata, $dto->metadata);
    }

    public function test_constructor_with_invalid_json_metadata(): void
    {
        // Arrange
        $invalidMetadata = '{ invalid json }';

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Metadata must be a valid JSON string');

        // Act
        new MintNftRequestDTO(
            chainId: 1,
            address: '0x123',
            toAddress: '0x456',
            amount: 1,
            metadata: $invalidMetadata
        );
    }

    public function test_constructor_with_empty_array_metadata(): void
    {
        // Arrange
        $validMetadata = json_encode([]);

        // Act
        $dto = new MintNftRequestDTO(
            chainId: 1,
            address: '0x123',
            toAddress: '0x456',
            amount: 1,
            metadata: $validMetadata
        );

        // Assert
        $this->assertEquals($validMetadata, $dto->metadata);
    }

    public function test_constructor_with_missing_required_fields(): void
    {
        // Arrange
        $invalidMetadata = json_encode([
            [
                'description' => 'A test NFT',
                'image' => 'https://example.com/image.png'
            ]
        ]);

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Metadata must contain both "name" and "image" fields');

        // Act
        new MintNftRequestDTO(
            chainId: 1,
            address: '0x123',
            toAddress: '0x456',
            amount: 1,
            metadata: $invalidMetadata
        );
    }

    public function test_constructor_with_invalid_name_field(): void
    {
        // Arrange
        $invalidMetadata = json_encode([
            [
                'name' => 123, // Should be string
                'description' => 'A test NFT',
                'image' => 'https://example.com/image.png'
            ]
        ]);

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Metadata "name" and "image" fields must be strings');

        // Act
        new MintNftRequestDTO(
            chainId: 1,
            address: '0x123',
            toAddress: '0x456',
            amount: 1,
            metadata: $invalidMetadata
        );
    }

    public function test_constructor_with_invalid_image_field(): void
    {
        // Arrange
        $invalidMetadata = json_encode([
            [
                'name' => 'Test NFT',
                'description' => 'A test NFT',
                'image' => 123 // Should be string
            ]
        ]);

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Metadata "name" and "image" fields must be strings');

        // Act
        new MintNftRequestDTO(
            chainId: 1,
            address: '0x123',
            toAddress: '0x456',
            amount: 1,
            metadata: $invalidMetadata
        );
    }

    public function test_constructor_with_invalid_attributes_not_array(): void
    {
        // Arrange
        $invalidMetadata = json_encode([
            [
                'name' => 'Test NFT',
                'description' => 'A test NFT',
                'image' => 'https://example.com/image.png',
                'attributes' => 'not-an-array'
            ]
        ]);

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Metadata "attributes" must be an array');

        // Act
        new MintNftRequestDTO(
            chainId: 1,
            address: '0x123',
            toAddress: '0x456',
            amount: 1,
            metadata: $invalidMetadata
        );
    }

    public function test_constructor_with_invalid_attribute_missing_fields(): void
    {
        // Arrange
        $invalidMetadata = json_encode([
            [
                'name' => 'Test NFT',
                'description' => 'A test NFT',
                'image' => 'https://example.com/image.png',
                'attributes' => [
                    [
                        'trait_type' => 'Rarity'
                        // Missing 'value' field
                    ]
                ]
            ]
        ]);

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Attribute must contain both "trait_type" and "value" fields');

        // Act
        new MintNftRequestDTO(
            chainId: 1,
            address: '0x123',
            toAddress: '0x456',
            amount: 1,
            metadata: $invalidMetadata
        );
    }

    public function test_constructor_with_invalid_attribute_trait_type_not_string(): void
    {
        // Arrange
        $invalidMetadata = json_encode([
            [
                'name' => 'Test NFT',
                'description' => 'A test NFT',
                'image' => 'https://example.com/image.png',
                'attributes' => [
                    [
                        'trait_type' => 123, // Should be string
                        'value' => 'Common'
                    ]
                ]
            ]
        ]);

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Attribute "trait_type" must be a string and "value" must be a string or numeric');

        // Act
        new MintNftRequestDTO(
            chainId: 1,
            address: '0x123',
            toAddress: '0x456',
            amount: 1,
            metadata: $invalidMetadata
        );
    }

    public function test_toArray_with_metadata(): void
    {
        // Arrange
        $metadata = json_encode([
            [
                'name' => 'Test NFT',
                'description' => 'A test NFT',
                'image' => 'https://example.com/image.png'
            ]
        ]);

        $dto = new MintNftRequestDTO(
            chainId: 1,
            address: '0x123',
            toAddress: '0x456',
            amount: 1,
            reference: 'test-ref',
            metadata: $metadata
        );

        // Act
        $array = $dto->toArray();

        // Assert
        $this->assertEquals([
            'chain_id' => 1,
            'address' => '0x123',
            'to_address' => '0x456',
            'amount' => 1,
            'reference' => 'test-ref',
            'metadata' => $metadata
        ], $array);
    }

    public function test_constructor_without_metadata(): void
    {
        // Act
        $dto = new MintNftRequestDTO(
            chainId: 1,
            address: '0x123',
            toAddress: '0x456',
            amount: 1
        );

        // Assert
        $this->assertNull($dto->metadata);
    }
}