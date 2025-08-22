<?php

namespace DVB\Core\SDK\DTOs;

use DVB\Core\SDK\DTOs\PaginatedCollectionDataDTO;

class CollectionListResponseDTO extends ApiResponse implements PaginatedResponseInterface
{
    /** @var PaginatedCollectionDataDTO|null */
    public mixed $data;

    public function __construct(int $code, string $message, ?PaginatedCollectionDataDTO $data = null)
    {
        parent::__construct($code, $message);
        $this->data = $data;
    }

    public static function fromArray(array $data): self
    {
        $paginatedData = isset($data['data']) ? PaginatedCollectionDataDTO::fromArray($data['data']) : null;

        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            $paginatedData
        );
    }

    public function getCursor(): ?string
    {
        return $this->data?->cursor;
    }

    public function hasMore(): bool
    {
        return $this->data?->hasMore ?? false;
    }

    public function getItems(): ?array
    {
        return $this->data?->items;
    }
}