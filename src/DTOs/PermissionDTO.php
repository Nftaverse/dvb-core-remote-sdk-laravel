<?php

namespace DVB\Core\SDK\DTOs;

class PermissionDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $description,
        public readonly ?string $createdAt = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? '',
            $data['name'] ?? '',
            $data['description'] ?? '',
            $data['createdAt'] ?? null,
        );
    }
}