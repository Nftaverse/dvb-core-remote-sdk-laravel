<?php

namespace DVB\Core\SDK\DTOs;

class PaginatedCollectionDataDTO implements PaginatedResponseInterface
{
    /** @var CollectionDTO[]|null */
    public ?array $items;
    public ?string $cursor;
    public bool $hasMore;

    /**
     * @param CollectionDTO[]|null $items
     * @param string|null $cursor
     * @param bool $hasMore
     */
    public function __construct(?array $items, ?string $cursor, bool $hasMore)
    {
        $this->items = $items;
        $this->cursor = $cursor;
        $this->hasMore = $hasMore;
    }

    public static function fromArray(array $data): self
    {
        $items = null;
        if (isset($data['data']) && is_array($data['data'])) {
            $items = [];
            foreach ($data['data'] as $itemData) {
                $items[] = CollectionDTO::fromArray($itemData);
            }
        }

        return new self(
            $items,
            $data['next_cursor'] ?? null,
            $data['hasMore'] ?? false,
        );
    }

    public function getCursor(): ?string
    {
        return $this->cursor;
    }

    public function hasMore(): bool
    {
        return $this->hasMore;
    }

    public function getItems(): ?array
    {
        return $this->items;
    }
}