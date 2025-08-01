<?php

namespace DVB\Core\SDK\DTOs;

class PaymentGatewayResponseDTO
{
    public function __construct(
        public readonly int $code,
        public readonly string $message,
        public readonly ?PaymentGatewayInfoDTO $data,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            isset($data['data']) ? PaymentGatewayInfoDTO::fromArray($data['data']) : null,
        );
    }
}