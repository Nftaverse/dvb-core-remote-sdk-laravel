<?php

namespace DVB\Core\SDK\DTOs;

class PaginatedNftDataDTO implements PaginatedResponseInterface
{
    /**
     * @param NftDTO[]|null $items
     * @param string|null $cursor
     * @param bool $hasMore
     */
    public function __construct(
        public readonly ?array $items,
        public readonly ?string $cursor,
        public readonly bool $hasMore,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $items = null;
        if (isset($data['items']) && is_array($data['items'])) {
            $items = [];
            foreach ($data['items'] as $itemData) {
                $items[] = NftDTO::fromArray($itemData);
            }
        }

        return new self(
            $items,
            $data['cursor'] ?? null,
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