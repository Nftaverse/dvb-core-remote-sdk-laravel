<?php

namespace DVB\Core\SDK;

use DVB\Core\SDK\DTOs\PaginatedResponseInterface;

class PaginationIterator
{
    private DvbApiClient $client;
    private string $method;
    private array $params;
    private ?string $cursor = null;
    private bool $hasMore = true;
    private ?PaginatedResponseInterface $currentResponse = null;

    public function __construct(DvbApiClient $client, string $method, array $params = [])
    {
        $this->client = $client;
        $this->method = $method;
        $this->params = $params;
    }

    public function current(): ?PaginatedResponseInterface
    {
        return $this->currentResponse;
    }

    public function next(): void
    {
        if (!$this->hasMore) {
            return;
        }

        // Add cursor to params if we have one
        $params = $this->params;
        if ($this->cursor) {
            $params['cursor'] = $this->cursor;
        }

        // Call the method on the client
        $this->currentResponse = call_user_func_array([$this->client, $this->method], $params);
        
        // Update cursor and hasMore
        if ($this->currentResponse) {
            $this->cursor = $this->currentResponse->getCursor();
            $this->hasMore = $this->currentResponse->hasMore();
        } else {
            $this->hasMore = false;
        }
    }

    public function hasNext(): bool
    {
        return $this->hasMore;
    }

    public function getAllItems(): array
    {
        $allItems = [];
        
        while ($this->hasNext()) {
            $this->next();
            if ($this->currentResponse && $this->currentResponse->getItems()) {
                $allItems = array_merge($allItems, $this->currentResponse->getItems());
            }
        }
        
        return $allItems;
    }
}