<?php

namespace DVB\Core\SDK\DTOs;

class PaymentMethodDTO
{
    public string $id;
    public string $name;
    public string $type;
    public bool $is_default;
    public ?string $createdAt;

    public function __construct(
        string $id,
        string $name,
        string $type,
        bool $is_default = false,
        ?string $createdAt = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->is_default = $is_default;
        $this->createdAt = $createdAt;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? '',
            $data['name'] ?? '',
            $data['type'] ?? '',
            $data['isDefault'] ?? false,
            $data['createdAt'] ?? null,
        );
    }
}