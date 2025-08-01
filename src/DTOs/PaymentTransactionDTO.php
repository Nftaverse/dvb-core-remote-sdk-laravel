<?php

namespace DVB\Core\SDK\DTOs;

class PaymentTransactionDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly string $currency,
        public readonly float $amount,
        public readonly ?string $transactionId = null,
        public readonly ?string $paymentMethod = null,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? '',
            $data['status'] ?? '',
            $data['currency'] ?? '',
            $data['amount'] ?? 0.0,
            $data['transactionId'] ?? null,
            $data['paymentMethod'] ?? null,
            $data['createdAt'] ?? null,
            $data['updatedAt'] ?? null,
        );
    }
}