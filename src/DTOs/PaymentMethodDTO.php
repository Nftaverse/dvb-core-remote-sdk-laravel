<?php

namespace DVB\Core\SDK\DTOs;

class PaymentMethodDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $type,
        public readonly bool $enabled,
        public readonly ?string $description = null,
        public readonly ?array $supportedCurrencies = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? '',
            $data['name'] ?? '',
            $data['type'] ?? '',
            $data['enabled'] ?? false,
            $data['description'] ?? null,
            $data['supportedCurrencies'] ?? null,
        );
    }
}