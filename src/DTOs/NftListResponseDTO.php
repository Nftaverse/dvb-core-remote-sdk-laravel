<?php

namespace DVB\Core\SDK\DTOs;

class NftListResponseDTO implements PaginatedResponseInterface
{
    public int $code;
    public string $message;
    public ?PaginatedNftDataDTO $data;

    public function __construct(
        int $code,
        string $message,
        ?PaginatedNftDataDTO $data,
    ) {
        $this->code = $code;
        $this->message = $message;
        $this->data = $data;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            isset($data['data']) ? PaginatedNftDataDTO::fromArray($data['data']) : null,
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