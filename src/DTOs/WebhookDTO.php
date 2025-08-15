<?php

namespace DVB\Core\SDK\DTOs;

class WebhookDTO
{
    public string $id;
    public string $url;
    public string $type;
    public ?string $name;
    public ?string $collectionAddress;
    public ?string $collectionChainId;
    public ?string $createdAt;
    public ?string $updatedAt;

    public function __construct(
        string $id,
        string $url,
        string $type,
        ?string $name = null,
        ?string $collectionAddress = null,
        ?string $collectionChainId = null,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->url = $url;
        $this->type = $type;
        $this->name = $name;
        $this->collectionAddress = $collectionAddress;
        $this->collectionChainId = $collectionChainId;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? '',
            $data['url'] ?? '',
            $data['type'] ?? '',
            $data['name'] ?? null,
            $data['collectionAddress'] ?? null,
            $data['collectionChainId'] ?? null,
            $data['createdAt'] ?? null,
            $data['updatedAt'] ?? null,
        );
    }
}