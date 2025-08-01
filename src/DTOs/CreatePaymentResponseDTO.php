<?php

namespace DVB\Core\SDK\DTOs;

class CreatePaymentResponseDTO
{
    public function __construct(
        public readonly int $code,
        public readonly string $message,
        public readonly ?PaymentLinkDTO $data,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            isset($data['data']) ? PaymentLinkDTO::fromArray($data['data']) : null,
        );
    }
}