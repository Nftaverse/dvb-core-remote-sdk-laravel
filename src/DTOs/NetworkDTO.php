<?php

namespace DVB\Core\SDK\DTOs;

class NetworkDTO
{
    public function __construct(
        public readonly int $chainId,
        public readonly string $name,
        public readonly string $symbol,
        public readonly string $type,
        public readonly bool $enabled,
        public readonly ?string $rpcUrl = null,
        public readonly ?string $explorerUrl = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['chainId'] ?? 0,
            $data['name'] ?? '',
            $data['symbol'] ?? '',
            $data['type'] ?? '',
            $data['enabled'] ?? false,
            $data['rpcUrl'] ?? null,
            $data['explorerUrl'] ?? null,
        );
    }
}