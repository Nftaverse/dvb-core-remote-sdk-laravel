<?php

namespace DVB\Core\SDK\Tests\Unit;

use DVB\Core\SDK\PaginationIterator;
use DVB\Core\SDK\Tests\TestCase;
use DVB\Core\SDK\DvbApiClient;
use DVB\Core\SDK\DTOs\NftListResponseDTO;
use DVB\Core\SDK\DTOs\NftDTO;

class PaginationIteratorTest extends TestCase
{
    public function test_it_can_be_instantiated()
    {
        $client = $this->createMock(DvbApiClient::class);
        $iterator = new PaginationIterator($client, 'getNftsByContract', ['address', 1]);

        $this->assertInstanceOf(PaginationIterator::class, $iterator);
    }

    public function test_iteration_works_correctly_for_multiple_pages()
    {
        // Arrange
        $client = $this->createMock(DvbApiClient::class);

        // Mock the first page response
        $firstPageResponse = $this->createMock(NftListResponseDTO::class);
        $firstPageResponse->method('getItems')->willReturn([
            new NftDTO('1', '0x123', 1, 'Test NFT 1', 'Description 1', 'image1.jpg'),
            new NftDTO('2', '0x123', 1, 'Test NFT 2', 'Description 2', 'image2.jpg'),
        ]);
        $firstPageResponse->method('getCursor')->willReturn('cursor1');
        $firstPageResponse->method('hasMore')->willReturn(true);

        // Mock the second page response
        $secondPageResponse = $this->createMock(NftListResponseDTO::class);
        $secondPageResponse->method('getItems')->willReturn([ new NftDTO('3', '0x123', 1, 'Test NFT 3', 'Description 3', 'image3.jpg') ]);
        $secondPageResponse->method('getCursor')->willReturn(null);
        $secondPageResponse->method('hasMore')->willReturn(false);

        $callCount = 0;
        $client->expects($this->exactly(2))
            ->method('getNftsByContract')
            ->willReturnCallback(function ($address, $chainId, $cursor = null) use ($firstPageResponse, $secondPageResponse, &$callCount) {
                $callCount++;
                if ($callCount === 1) {
                    $this->assertEquals('address', $address);
                    $this->assertEquals(1, $chainId);
                    $this->assertNull($cursor);
                    return $firstPageResponse;
                } elseif ($callCount === 2) {
                    $this->assertEquals('address', $address);
                    $this->assertEquals(1, $chainId);
                    $this->assertEquals('cursor1', $cursor);
                    return $secondPageResponse;
                }
                $this->fail('Method was called more than twice');
            });

        $iterator = new PaginationIterator($client, 'getNftsByContract', ['address', 1]);
        
        // Act
        $results = [];
        foreach ($iterator as $item) {
            $results[] = $item;
        }

        // Assert
        $this->assertCount(3, $results);
        $this->assertInstanceOf(NftDTO::class, $results[0]);
        $this->assertEquals('1', $results[0]->tokenId);
        $this->assertEquals('2', $results[1]->tokenId);
        $this->assertEquals('3', $results[2]->tokenId);
    }
    
    public function test_get_all_items_collects_from_all_pages()
    {
        // Arrange
        $client = $this->createMock(DvbApiClient::class);
        $firstPageResponse = $this->createMock(NftListResponseDTO::class);
        $firstPageResponse->method('getItems')->willReturn([new NftDTO('1', '0x123', 1, 'Test NFT 1', 'Description 1', 'image1.jpg')]);
        $firstPageResponse->method('getCursor')->willReturn('cursor1');
        $firstPageResponse->method('hasMore')->willReturn(true);

        $secondPageResponse = $this->createMock(NftListResponseDTO::class);
        $secondPageResponse->method('getItems')->willReturn([new NftDTO('2', '0x123', 1, 'Test NFT 2', 'Description 2', 'image2.jpg')]);
        $secondPageResponse->method('getCursor')->willReturn(null);
        $secondPageResponse->method('hasMore')->willReturn(false);

        $client->method('getNftsByContract')
            ->willReturnOnConsecutiveCalls($firstPageResponse, $secondPageResponse);

        $iterator = new PaginationIterator($client, 'getNftsByContract', ['address', 1]);

        // Act
        $allItems = $iterator->getAllItems();
        
        // Assert
        $this->assertIsArray($allItems);
        $this->assertCount(2, $allItems);
        $this->assertEquals('1', $allItems[0]->tokenId);
        $this->assertEquals('2', $allItems[1]->tokenId);
    }

    public function test_iteration_handles_empty_response()
    {
        // Arrange
        $client = $this->createMock(DvbApiClient::class);
        $emptyResponse = $this->createMock(NftListResponseDTO::class);
        $emptyResponse->method('getItems')->willReturn([]);
        $emptyResponse->method('hasMore')->willReturn(false);

        $client->expects($this->once())->method('getNftsByContract')->willReturn($emptyResponse);
        $iterator = new PaginationIterator($client, 'getNftsByContract', ['address', 1]);
        
        // Act
        $results = [];
        foreach ($iterator as $item) {
            $results[] = $item;
        }
        
        // Assert
        $this->assertCount(0, $results);
    }

    public function test_iteration_handles_client_returning_null()
    {
        // Arrange
        $client = $this->createMock(DvbApiClient::class);
        $emptyResponse = $this->createMock(NftListResponseDTO::class);
        $emptyResponse->method('getItems')->willReturn([]);
        $emptyResponse->method('hasMore')->willReturn(false);
        $client->method('getNftsByContract')->willReturn($emptyResponse);
        
        $iterator = new PaginationIterator($client, 'getNftsByContract', ['address', 1]);
        
        // Act
        $results = [];
        foreach ($iterator as $item) {
            $results[] = $item;
        }
        
        // Assert
        $this->assertCount(0, $results);
    }
}
