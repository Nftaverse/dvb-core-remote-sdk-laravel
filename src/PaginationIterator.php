<?php

namespace DVB\Core\SDK;

use Iterator;

class PaginationIterator implements Iterator
{
    private DvbApiClient $client;
    private string $method;
    private array $params;
    private ?string $cursor = null;
    private bool $hasMore = true;
    private array $items = [];
    private int $position = 0;

    public function __construct(DvbApiClient $client, string $method, array $params = [])
    {
        $this->client = $client;
        $this->method = $method;
        $this->params = $params;
    }

    public function current(): mixed
    {
        return $this->items[$this->position] ?? null;
    }

    public function next(): void
    {
        $this->position++;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        // If we have more items in the current buffer, we're valid.
        if (isset($this->items[$this->position])) {
            return true;
        }

        // If there are no more pages to fetch, we're done.
        if (!$this->hasMore) {
            return false;
        }
        
        // Fetch the next page of results.
        $this->fetchNextPage();

        // After fetching, check again if the current position is valid.
        return isset($this->items[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
        $this->items = [];
        $this->cursor = null;
        $this->hasMore = true;
    }

    private function fetchNextPage(): void
    {
        // Prepare parameters for the method call
        $params = $this->params;
        if ($this->cursor) {
            // If cursor exists, replace or add it as the last parameter
            if (count($params) >= 3) {
                // If there are at least 3 params, assume the 3rd is cursor and replace it
                $params[2] = $this->cursor;
            } else {
                // Otherwise, add it as a new parameter
                $params[] = $this->cursor;
            }
        }

        // Call the method on the client
        /** @var DTOs\PaginatedResponseInterface|null $response */
        $response = call_user_func_array([$this->client, $this->method], $params);
        
        // Update items, cursor and hasMore
        if ($response && is_array($response->getItems())) {
            $this->items = $response->getItems();
            $this->cursor = $response->getCursor();
            $this->hasMore = $response->hasMore();
            $this->position = 0; // Reset position to the start of the new items
        } else {
            $this->items = [];
            $this->hasMore = false;
        }
    }

    public function getCursor(): ?string
    {
        return $this->cursor;
    }

    public function getAllItems(): array
    {
        $allItems = [];
        $this->rewind();

        while ($this->valid()) {
            $allItems[] = $this->current();
            $this->next();
        }

        return $allItems;
    }
}
