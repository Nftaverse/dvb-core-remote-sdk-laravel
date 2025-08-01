<?php

namespace DVB\Core\SDK\Tests\Unit;

use DVB\Core\SDK\PaginationIterator;
use DVB\Core\SDK\Tests\TestCase;
use DVB\Core\SDK\DvbApiClient;
use DVB\Core\SDK\DTOs\PaginatedNftDataDTO;
use DVB\Core\SDK\DTOs\NftDTO;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Log\LoggerInterface;

class PaginationIteratorTest extends TestCase
{
    public function test_it_can_be_instantiated()
    {
        $client = $this->createMock(DvbApiClient::class);
        $iterator = new PaginationIterator($client, 'getNftsByContract', ['address', 1]);

        $this->assertInstanceOf(PaginationIterator::class, $iterator);
    }

    public function test_next_method_calls_client_method_with_correct_parameters()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        // Create a mock response for the first page
        $firstPageResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'items' => [
                    ['tokenId' => '1', 'name' => 'NFT 1', 'contractAddress' => '0x123', 'chainId' => 1],
                ],
                'cursor' => 'cursor1',
                'hasMore' => true
            ]
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->with('GET', 'https://api.dvb.com/nft/address', $this->callback(function ($options) {
                // Verify no cursor in first request
                return !isset($options['query']['cursor']);
            }))
            ->willReturn(new Response(200, [], json_encode($firstPageResponse)));
        
        $iterator = new PaginationIterator($client, 'getNftsByContract', ['address', 1]);
        
        // Act
        $iterator->next();
        
        // Assert
        $current = $iterator->current();
        $this->assertInstanceOf(PaginatedNftDataDTO::class, $current);
        $this->assertTrue($iterator->hasNext());
        $this->assertEquals('cursor1', $current->getCursor());
    }

    public function test_next_method_calls_client_method_with_cursor_on_subsequent_calls()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        // Create mock responses for multiple pages
        $firstPageResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'items' => [
                    ['tokenId' => '1', 'name' => 'NFT 1', 'contractAddress' => '0x123', 'chainId' => 1],
                ],
                'cursor' => 'cursor1',
                'hasMore' => true
            ]
        ];
        
        $secondPageResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'items' => [
                    ['tokenId' => '2', 'name' => 'NFT 2', 'contractAddress' => '0x123', 'chainId' => 1],
                ],
                'cursor' => null,
                'hasMore' => false
            ]
        ];
        
        $httpClient->expects($this->exactly(2))
            ->method('request')
            ->withConsecutive(
                [$this->equalTo('GET'), $this->equalTo('https://api.dvb.com/nft/address'), $this->callback(function ($options) {
                    return !isset($options['query']['cursor']);
                })],
                [$this->equalTo('GET'), $this->equalTo('https://api.dvb.com/nft/address'), $this->callback(function ($options) {
                    return isset($options['query']['cursor']) && $options['query']['cursor'] === 'cursor1';
                })]
            )
            ->willReturnOnConsecutiveCalls(
                new Response(200, [], json_encode($firstPageResponse)),
                new Response(200, [], json_encode($secondPageResponse))
            );
        
        $iterator = new PaginationIterator($client, 'getNftsByContract', ['address', 1]);
        
        // Act & Assert - First call
        $iterator->next();
        $current = $iterator->current();
        $this->assertInstanceOf(PaginatedNftDataDTO::class, $current);
        $this->assertTrue($iterator->hasNext());
        $this->assertEquals('cursor1', $current->getCursor());
        
        // Act & Assert - Second call
        $iterator->next();
        $current = $iterator->current();
        $this->assertInstanceOf(PaginatedNftDataDTO::class, $current);
        $this->assertFalse($iterator->hasNext());
        $this->assertNull($current->getCursor());
    }

    public function test_has_next_returns_false_when_no_more_pages()
    {
        // Arrange
        $client = $this->createMock(DvbApiClient::class);
        $iterator = new PaginationIterator($client, 'getNftsByContract', ['address', 1]);
        
        // Mock the currentResponse to have no more pages
        $paginatedData = $this->createMock(PaginatedNftDataDTO::class);
        $paginatedData->method('hasMore')->willReturn(false);
        $paginatedData->method('getCursor')->willReturn(null);
        
        // Use reflection to set the private property
        $reflection = new \ReflectionClass($iterator);
        $currentResponseProperty = $reflection->getProperty('currentResponse');
        $currentResponseProperty->setAccessible(true);
        $currentResponseProperty->setValue($iterator, $paginatedData);
        
        $hasMoreProperty = $reflection->getProperty('hasMore');
        $hasMoreProperty->setAccessible(true);
        $hasMoreProperty->setValue($iterator, false);
        
        // Act & Assert
        $this->assertFalse($iterator->hasNext());
    }

    public function test_has_next_returns_true_when_more_pages_available()
    {
        // Arrange
        $client = $this->createMock(DvbApiClient::class);
        $iterator = new PaginationIterator($client, 'getNftsByContract', ['address', 1]);
        
        // Mock the currentResponse to have more pages
        $paginatedData = $this->createMock(PaginatedNftDataDTO::class);
        $paginatedData->method('hasMore')->willReturn(true);
        $paginatedData->method('getCursor')->willReturn('next_cursor');
        
        // Use reflection to set the private property
        $reflection = new \ReflectionClass($iterator);
        $currentResponseProperty = $reflection->getProperty('currentResponse');
        $currentResponseProperty->setAccessible(true);
        $currentResponseProperty->setValue($iterator, $paginatedData);
        
        $hasMoreProperty = $reflection->getProperty('hasMore');
        $hasMoreProperty->setAccessible(true);
        $hasMoreProperty->setValue($iterator, true);
        
        // Act & Assert
        $this->assertTrue($iterator->hasNext());
    }

    public function test_get_all_items_collects_items_from_all_pages()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        // Create mock responses for multiple pages
        $firstPageResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'items' => [
                    ['tokenId' => '1', 'name' => 'NFT 1', 'contractAddress' => '0x123', 'chainId' => 1],
                    ['tokenId' => '2', 'name' => 'NFT 2', 'contractAddress' => '0x123', 'chainId' => 1],
                ],
                'cursor' => 'cursor1',
                'hasMore' => true
            ]
        ];
        
        $secondPageResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'items' => [
                    ['tokenId' => '3', 'name' => 'NFT 3', 'contractAddress' => '0x123', 'chainId' => 1],
                ],
                'cursor' => null,
                'hasMore' => false
            ]
        ];
        
        $httpClient->expects($this->exactly(2))
            ->method('request')
            ->willReturnOnConsecutiveCalls(
                new Response(200, [], json_encode($firstPageResponse)),
                new Response(200, [], json_encode($secondPageResponse))
            );
        
        $iterator = new PaginationIterator($client, 'getNftsByContract', ['address', 1]);
        
        // Act
        $allItems = $iterator->getAllItems();
        
        // Assert
        $this->assertIsArray($allItems);
        $this->assertCount(3, $allItems);
        $this->assertInstanceOf(NftDTO::class, $allItems[0]);
        $this->assertEquals('1', $allItems[0]->tokenId);
        $this->assertEquals('2', $allItems[1]->tokenId);
        $this->assertEquals('3', $allItems[2]->tokenId);
    }

    public function test_get_all_items_handles_empty_response()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        // Create mock response for empty result
        $emptyResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'items' => [],
                'cursor' => null,
                'hasMore' => false
            ]
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->willReturn(new Response(200, [], json_encode($emptyResponse)));
        
        $iterator = new PaginationIterator($client, 'getNftsByContract', ['address', 1]);
        
        // Act
        $allItems = $iterator->getAllItems();
        
        // Assert
        $this->assertIsArray($allItems);
        $this->assertCount(0, $allItems);
    }

    public function test_get_all_items_handles_single_page_response()
    {
        // Arrange
        $httpClient = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $client = new DvbApiClient($httpClient, $logger, 'test-key');
        
        // Create mock response for single page
        $singlePageResponse = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'items' => [
                    ['tokenId' => '1', 'name' => 'NFT 1', 'contractAddress' => '0x123', 'chainId' => 1],
                    ['tokenId' => '2', 'name' => 'NFT 2', 'contractAddress' => '0x123', 'chainId' => 1],
                ],
                'cursor' => null,
                'hasMore' => false
            ]
        ];
        
        $httpClient->expects($this->once())
            ->method('request')
            ->willReturn(new Response(200, [], json_encode($singlePageResponse)));
        
        $iterator = new PaginationIterator($client, 'getNftsByContract', ['address', 1]);
        
        // Act
        $allItems = $iterator->getAllItems();
        
        // Assert
        $this->assertIsArray($allItems);
        $this->assertCount(2, $allItems);
        $this->assertInstanceOf(NftDTO::class, $allItems[0]);
        $this->assertEquals('1', $allItems[0]->tokenId);
        $this->assertEquals('2', $allItems[1]->tokenId);
    }

    public function test_iterator_handles_null_current_response()
    {
        // Arrange
        $client = $this->createMock(DvbApiClient::class);
        $iterator = new PaginationIterator($client, 'getNftsByContract', ['address', 1]);
        
        // Use reflection to set the private property to null
        $reflection = new \ReflectionClass($iterator);
        $currentResponseProperty = $reflection->getProperty('currentResponse');
        $currentResponseProperty->setAccessible(true);
        $currentResponseProperty->setValue($iterator, null);
        
        // Act
        $iterator->next();
        
        // Assert
        $this->assertNull($iterator->current());
        $this->assertFalse($iterator->hasNext());
    }
}