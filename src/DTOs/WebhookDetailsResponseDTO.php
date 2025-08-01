<?php

namespace DVB\Core\SDK\DTOs;

class WebhookDetailsResponseDTO
{
    public function __construct(
        public readonly int $code,
        public readonly string $message,
        public readonly ?WebhookDTO $data,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            isset($data['data']) ? WebhookDTO::fromArray($data['data']) : null,
        );
    }
}