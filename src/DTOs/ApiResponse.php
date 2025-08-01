<?php

namespace DVB\Core\SDK\DTOs;

class ApiResponse
{
    public function __construct(
        public readonly int $code,
        public readonly string $message,
        public readonly ?array $data = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            $data['data'] ?? null,
        );
    }
}