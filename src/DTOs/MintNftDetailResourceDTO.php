<?php

namespace DVB\Core\SDK\DTOs;

class MintNftDetailResourceDTO
{
    public function __construct(
        public readonly int $token_id,
        public readonly string $network_id,
        public readonly string $token_address,
        public readonly string $contract_type,
        public readonly string $name,
        public readonly string $status,
        public readonly string $description,
        public readonly string $asset,
        public readonly string $asset_type,
        public readonly string $minter_address,
        public readonly string $reference,
        public readonly string $created_at,
        public readonly string $updated_at,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['token_id'] ?? 0,
            $data['network_id'] ?? '',
            $data['token_address'] ?? '',
            $data['contract_type'] ?? '',
            $data['name'] ?? '',
            $data['status'] ?? '',
            $data['description'] ?? '',
            $data['asset'] ?? '',
            $data['asset_type'] ?? '',
            $data['minter_address'] ?? '',
            $data['reference'] ?? '',
            $data['created_at'] ?? '',
            $data['updated_at'] ?? '',
        );
    }
}