<?php

namespace DVB\Core\SDK\DTOs;

class PaginatedWebhookDataDTO implements PaginatedResponseInterface
{
    /** @var WebhookDTO[]|null */
    public ?array $items;
    public ?string $cursor;
    public bool $hasMore;

    /**
     * @param WebhookDTO[]|null $items
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
        if (isset($data['items']) && is_array($data['items'])) {
            $items = [];
            foreach ($data['items'] as $itemData) {
                $items[] = WebhookDTO::fromArray($itemData);
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
