<?php

namespace DVB\Core\SDK\DTOs;

class NftMetadataResponseDTO
{
    public function __construct(
        public readonly int $code,
        public readonly string $message,
        public readonly ?NftMetadataDTO $data,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            isset($data['data']) ? NftMetadataDTO::fromArray($data['data']) : null,
        );
    }
}