<?php

namespace DVB\Core\SDK\DTOs;

class PermissionDTO
{
    public string $id;
    public string $name;
    public ?string $description;
    public ?string $createdAt;

    public function __construct(string $id, string $name, ?string $description, ?string $createdAt = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->createdAt = $createdAt;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? '',
            $data['name'] ?? '',
            $data['description'] ?? null,
            $data['createdAt'] ?? null,
        );
    }
}