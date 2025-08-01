<?php

namespace DVB\Core\SDK\DTOs;

class NftListResponseDTO
{
    public function __construct(
        public readonly int $code,
        public readonly string $message,
        public readonly ?PaginatedNftDataDTO $data,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            isset($data['data']) ? PaginatedNftDataDTO::fromArray($data['data']) : null,
        );
    }
}