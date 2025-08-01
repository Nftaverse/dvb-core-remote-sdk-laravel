<?php

namespace DVB\Core\SDK\DTOs;

class WebhookDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $url,
        public readonly string $type,
        public readonly ?string $name = null,
        public readonly ?string $collectionAddress = null,
        public readonly ?string $collectionChainId = null,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? '',
            $data['url'] ?? '',
            $data['type'] ?? '',
            $data['name'] ?? null,
            $data['collectionAddress'] ?? null,
            $data['collectionChainId'] ?? null,
            $data['createdAt'] ?? null,
            $data['updatedAt'] ?? null,
        );
    }
}