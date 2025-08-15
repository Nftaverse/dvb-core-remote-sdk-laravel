<?php

namespace DVB\Core\SDK\DTOs;

class UserNftResponseDTO extends ApiResponse implements PaginatedResponseInterface
{
    /** @var PaginatedNftDataDTO|null */
    public mixed $data;

    public function __construct(int $code, string $message, ?PaginatedNftDataDTO $data = null)
    {
        parent::__construct($code, $message);
        $this->data = $data;
    }

    public static function fromArray(array $data): self
    {
        $paginatedData = isset($data['data']) ? PaginatedNftDataDTO::fromArray($data['data']) : null;
        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            $paginatedData
        );
    }

    public function getNextCursor(): ?string
    {
        return $this->data?->next_cursor;
    }

    public function getItems(): array
    {
        return $this->data?->data ?? [];
    }
}
