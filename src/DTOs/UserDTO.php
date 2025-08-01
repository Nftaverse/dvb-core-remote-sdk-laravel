<?php

namespace DVB\Core\SDK\DTOs;

class UserDTO
{
    public function __construct(
        public readonly string $uid,
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $avatar = null,
        public readonly ?string $phone = null,
        public readonly ?array $wallets = null,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['uid'] ?? '',
            $data['name'] ?? '',
            $data['email'] ?? '',
            $data['avatar'] ?? null,
            $data['phone'] ?? null,
            $data['wallets'] ?? null,
            $data['createdAt'] ?? null,
            $data['updatedAt'] ?? null,
        );
    }
}