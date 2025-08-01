<?php

namespace DVB\Core\SDK\DTOs;

class UserResponseDTO
{
    public function __construct(
        public readonly int $code,
        public readonly string $message,
        public readonly ?UserDTO $data,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            isset($data['data']) ? UserDTO::fromArray($data['data']) : null,
        );
    }
}