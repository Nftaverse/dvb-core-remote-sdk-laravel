<?php

namespace DVB\Core\SDK\DTOs;

class NftAttributeDTO
{
    public function __construct(
        public readonly string $traitType,
        public readonly string $value,
        public readonly ?string $displayType = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['trait_type'] ?? $data['traitType'] ?? '',
            $data['value'] ?? '',
            $data['display_type'] ?? $data['displayType'] ?? null,
        );
    }
}