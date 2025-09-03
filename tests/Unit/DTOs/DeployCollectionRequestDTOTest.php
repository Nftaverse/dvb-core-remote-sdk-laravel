<?php

namespace DVB\Core\SDK\Tests\Unit\DTOs;

use DVB\Core\SDK\DTOs\DeployCollectionRequestDTO;
use DVB\Core\SDK\Tests\TestCase;

class DeployCollectionRequestDTOTest extends TestCase
{
    public function test_it_can_be_instantiated_with_all_parameters(): void
    {
        // Arrange
        $imageResource = fopen('php://memory', 'rb');
        
        // Act
        $dto = new DeployCollectionRequestDTO(
            chainId: 1,
            ownerAddress: '0xowner',
            name: 'Test Collection',
            quantity: 100,
            enableFlexibleMint: true,
            enableSoulbound: false,
            imageResource: $imageResource,
            description: 'Test Description',
            symbol: 'TEST',
            team: [['name' => 'Test Team']],
            royalty: [['address' => '0xroyalty', 'percentage' => 10]]
        );
        
        // Assert
        $this->assertEquals(1, $dto->chainId);
        $this->assertEquals('0xowner', $dto->ownerAddress);
        $this->assertEquals('Test Collection', $dto->name);
        $this->assertEquals(100, $dto->quantity);
        $this->assertTrue($dto->enableFlexibleMint);
        $this->assertFalse($dto->enableSoulbound);
        $this->assertSame($imageResource, $dto->imageResource);
        $this->assertEquals('Test Description', $dto->description);
        $this->assertEquals('TEST', $dto->symbol);
        $this->assertEquals([['name' => 'Test Team']], $dto->team);
        $this->assertEquals([['address' => '0xroyalty', 'percentage' => 10]], $dto->royalty);
        
        // Clean up
        fclose($imageResource);
    }
    
    public function test_it_throws_exception_when_image_resource_is_not_provided(): void
    {
        // Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Image resource is required and must be a valid resource');
        
        // Act
        new DeployCollectionRequestDTO(
            chainId: 1,
            ownerAddress: '0xowner',
            name: 'Test Collection',
            quantity: 100,
            enableFlexibleMint: true,
            enableSoulbound: false,
            imageResource: null
        );
    }
    
    public function test_it_throws_exception_when_image_resource_is_invalid(): void
    {
        // Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Image resource is required and must be a valid resource');
        
        // Act
        new DeployCollectionRequestDTO(
            chainId: 1,
            ownerAddress: '0xowner',
            name: 'Test Collection',
            quantity: 100,
            enableFlexibleMint: true,
            enableSoulbound: false,
            imageResource: 'invalid'
        );
    }
    
    public function test_to_array_returns_correct_data(): void
    {
        // Arrange
        $imageResource = fopen('php://memory', 'rb');
        $dto = new DeployCollectionRequestDTO(
            chainId: 1,
            ownerAddress: '0xowner',
            name: 'Test Collection',
            quantity: 100,
            enableFlexibleMint: true,
            enableSoulbound: false,
            imageResource: $imageResource,
            description: 'Test Description',
            symbol: 'TEST',
            team: [['name' => 'Test Team']],
            royalty: [['address' => '0xroyalty', 'percentage' => 10]]
        );
        
        // Act
        $array = $dto->toArray();
        
        // Assert
        $this->assertEquals([
            'chain_id' => 1,
            'owner_address' => '0xowner',
            'name' => 'Test Collection',
            'quantity' => 100,
            'enable_flexible_mint' => true,
            'enable_soulbound' => false,
            'description' => 'Test Description',
            'symbol' => 'TEST',
            'team' => '[{"name":"Test Team"}]',
            'royalty' => '[{"address":"0xroyalty","percentage":10}]'
        ], $array);
        
        // Clean up
        fclose($imageResource);
    }
    
    public function test_to_array_without_optional_parameters(): void
    {
        // Arrange
        $imageResource = fopen('php://memory', 'rb');
        $dto = new DeployCollectionRequestDTO(
            chainId: 1,
            ownerAddress: '0xowner',
            name: 'Test Collection',
            quantity: 100,
            enableFlexibleMint: true,
            enableSoulbound: false,
            imageResource: $imageResource
        );
        
        // Act
        $array = $dto->toArray();
        
        // Assert
        $this->assertEquals([
            'chain_id' => 1,
            'owner_address' => '0xowner',
            'name' => 'Test Collection',
            'quantity' => 100,
            'enable_flexible_mint' => true,
            'enable_soulbound' => false,
        ], $array);
        
        // Clean up
        fclose($imageResource);
    }
    
    public function test_has_image_returns_true_when_image_resource_is_valid(): void
    {
        // Arrange
        $imageResource = fopen('php://memory', 'rb');
        $dto = new DeployCollectionRequestDTO(
            chainId: 1,
            ownerAddress: '0xowner',
            name: 'Test Collection',
            quantity: 100,
            enableFlexibleMint: true,
            enableSoulbound: false,
            imageResource: $imageResource
        );
        
        // Act
        $hasImage = $dto->hasImage();
        
        // Assert
        $this->assertTrue($hasImage);
        
        // Clean up
        fclose($imageResource);
    }
    
    public function test_has_image_returns_false_when_image_resource_is_closed(): void
    {
        // Arrange
        $imageResource = fopen('php://memory', 'rb');
        $dto = new DeployCollectionRequestDTO(
            chainId: 1,
            ownerAddress: '0xowner',
            name: 'Test Collection',
            quantity: 100,
            enableFlexibleMint: true,
            enableSoulbound: false,
            imageResource: $imageResource
        );
        
        // Close the resource first
        fclose($imageResource);
        
        // Act
        $hasImage = $dto->hasImage();
        
        // Assert
        $this->assertFalse($hasImage);
    }
}