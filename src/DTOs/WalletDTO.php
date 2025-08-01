<?php

namespace DVB\Core\SDK\DTOs;

class WalletDTO
{
    public function __construct(
        public readonly string $address,
        public readonly int $chainId,
        public readonly ?string $name = null,
        public readonly ?string $createdAt = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['address'] ?? '',
            $data['chainId'] ?? 0,
            $data['name'] ?? null,
            $data['createdAt'] ?? null,
        );
    }
}