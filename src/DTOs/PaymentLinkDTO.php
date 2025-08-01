<?php

namespace DVB\Core\SDK\DTOs;

class PaymentLinkDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $url,
        public readonly string $status,
        public readonly string $currency,
        public readonly float $amount,
        public readonly ?string $description = null,
        public readonly ?string $expiresAt = null,
        public readonly ?string $createdAt = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? '',
            $data['url'] ?? '',
            $data['status'] ?? '',
            $data['currency'] ?? '',
            $data['amount'] ?? 0.0,
            $data['description'] ?? null,
            $data['expiresAt'] ?? null,
            $data['createdAt'] ?? null,
        );
    }
}