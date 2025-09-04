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
        $blindImageResource = fopen('php://memory', 'rb');
        
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
            imageUrl: 'https://example.com/image.jpg',
            contractMetadataUrl: 'https://example.com/metadata',
            contractBaseUrl: 'https://example.com/base',
            team: 'Test Team',
            roadmap: 'Phase 1',
            enableOwnerSignature: true,
            royalty: 5,
            receiveRoyaltyAddress: '0xroyalty',
            enableParentContract: true,
            parentContractAddress: '0xparent',
            enableBlind: true,
            blindName: 'Blind Collection',
            blindDescription: 'Blind Description',
            blindMetadataBaseUri: 'https://example.com/blind',
            blindImageResource: $blindImageResource,
            blindImageUrl: 'https://example.com/blind.jpg'
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
        $this->assertEquals('https://example.com/image.jpg', $dto->imageUrl);
        $this->assertEquals('https://example.com/metadata', $dto->contractMetadataUrl);
        $this->assertEquals('https://example.com/base', $dto->contractBaseUrl);
        $this->assertEquals('Test Team', $dto->team);
        $this->assertEquals('Phase 1', $dto->roadmap);
        $this->assertTrue($dto->enableOwnerSignature);
        $this->assertEquals(5, $dto->royalty);
        $this->assertEquals('0xroyalty', $dto->receiveRoyaltyAddress);
        $this->assertTrue($dto->enableParentContract);
        $this->assertEquals('0xparent', $dto->parentContractAddress);
        $this->assertTrue($dto->enableBlind);
        $this->assertEquals('Blind Collection', $dto->blindName);
        $this->assertEquals('Blind Description', $dto->blindDescription);
        $this->assertEquals('https://example.com/blind', $dto->blindMetadataBaseUri);
        $this->assertSame($blindImageResource, $dto->blindImageResource);
        $this->assertEquals('https://example.com/blind.jpg', $dto->blindImageUrl);
        
        // Clean up
        fclose($imageResource);
        fclose($blindImageResource);
    }
    
    public function test_it_throws_exception_when_image_resource_is_not_provided(): void
    {
        // Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Either image resource or image URL must be provided');
        
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
        $this->expectExceptionMessage('Image resource must be a valid resource');
        
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
        $blindImageResource = fopen('php://memory', 'rb');
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
            imageUrl: 'https://example.com/image.jpg',
            contractMetadataUrl: 'https://example.com/metadata',
            contractBaseUrl: 'https://example.com/base',
            team: 'Test Team',
            roadmap: 'Phase 1',
            enableOwnerSignature: true,
            royalty: 5,
            receiveRoyaltyAddress: '0xroyalty',
            enableParentContract: true,
            parentContractAddress: '0xparent',
            enableBlind: true,
            blindName: 'Blind Collection',
            blindDescription: 'Blind Description',
            blindMetadataBaseUri: 'https://example.com/blind',
            blindImageResource: $blindImageResource,
            blindImageUrl: 'https://example.com/blind.jpg'
        );
        
        // Act
        $array = $dto->toArray();
        
        // Assert
        $this->assertEquals([
            'chain_id' => 1,
            'owner_address' => '0xowner',
            'name' => 'Test Collection',
            'quantity' => 100,
            'enable_flexible_mint' => '1',
            'enable_soulbound' => '0',
            'description' => 'Test Description',
            'symbol' => 'TEST',
            'image_url' => 'https://example.com/image.jpg',
            'contract_metadata_url' => 'https://example.com/metadata',
            'contract_base_url' => 'https://example.com/base',
            'team' => '"Test Team"',
            'roadmap' => '"Phase 1"',
            'enable_owner_signature' => '1',
            'royalty' => 5,
            'receive_royalty_address' => '0xroyalty',
            'enable_parent_contract' => '1',
            'parent_contract_address' => '0xparent',
            'enable_blind' => '1',
            'blind_name' => 'Blind Collection',
            'blind_description' => 'Blind Description',
            'blind_metadata_base_uri' => 'https://example.com/blind',
            'blind_image_url' => 'https://example.com/blind.jpg'
        ], $array);
        
        // Clean up
        fclose($imageResource);
        fclose($blindImageResource);
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
            'enable_flexible_mint' => '1',
            'enable_soulbound' => '0',
            'enable_blind' => '0',
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
    
    public function test_has_blind_image_returns_true_when_blind_image_resource_is_valid(): void
    {
        // Arrange
        $imageResource = fopen('php://memory', 'rb');
        $blindImageResource = fopen('php://memory', 'rb');
        $dto = new DeployCollectionRequestDTO(
            chainId: 1,
            ownerAddress: '0xowner',
            name: 'Test Collection',
            quantity: 100,
            enableFlexibleMint: true,
            enableSoulbound: false,
            imageResource: $imageResource,
            blindImageResource: $blindImageResource
        );
        
        // Act
        $hasBlindImage = $dto->hasBlindImage();
        
        // Assert
        $this->assertTrue($hasBlindImage);
        
        // Clean up
        fclose($imageResource);
        fclose($blindImageResource);
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