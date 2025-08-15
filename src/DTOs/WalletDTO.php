<?php

namespace DVB\Core\SDK\DTOs;

class WalletDTO
{
    public function __construct(
        public readonly string $wallet_address,
        public readonly bool $is_treasury,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['wallet_address'] ?? '',
            $data['is_treasury'] ?? false,
        );
    }
}