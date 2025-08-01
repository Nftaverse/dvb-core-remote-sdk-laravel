<?php

namespace DVB\Core\SDK\DTOs;

class CheckPermissionResponseDTO
{
    public function __construct(
        public readonly int $code,
        public readonly string $message,
        public readonly ?bool $data,
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