<?php

namespace DVB\Core\SDK\DTOs;

class DeployCollectionWithImageRequestDTO
{
    /** @var int */
    public int $chainId;
    
    /** @var string */
    public string $ownerAddress;
    
    /** @var string */
    public string $name;
    
    /** @var int */
    public int $quantity;
    
    /** @var bool */
    public bool $enableFlexibleMint;
    
    /** @var bool */
    public bool $enableSoulbound;
    
    /** @var resource */
    public $imageResource;
    
    /** @var string|null */
    public ?string $description;
    
    /** @var string|null */
    public ?string $symbol;
    
    /** @var array|null */
    public ?array $team;
    
    /** @var array|null */
    public ?array $royalty;

    public function __construct(
        int $chainId,
        string $ownerAddress,
        string $name,
        int $quantity,
        bool $enableFlexibleMint,
        bool $enableSoulbound,
        $imageResource,
        ?string $description = null,
        ?string $symbol = null,
        ?array $team = null,
        ?array $royalty = null
    ) {
        // 驗證圖片資源
        if (!is_resource($imageResource)) {
            throw new \InvalidArgumentException('Image resource is required and must be a valid resource');
        }
        
        $this->chainId = $chainId;
        $this->ownerAddress = $ownerAddress;
        $this->name = $name;
        $this->quantity = $quantity;
        $this->enableFlexibleMint = $enableFlexibleMint;
        $this->enableSoulbound = $enableSoulbound;
        $this->imageResource = $imageResource;
        $this->description = $description;
        $this->symbol = $symbol;
        $this->team = $team;
        $this->royalty = $royalty;
    }

    public function toArray(): array
    {
        $data = [
            'chain_id' => $this->chainId,
            'owner_address' => $this->ownerAddress,
            'name' => $this->name,
            'quantity' => $this->quantity,
            'enable_flexible_mint' => $this->enableFlexibleMint,
            'enable_soulbound' => $this->enableSoulbound,
        ];

        if ($this->description !== null) {
            $data['description'] = $this->description;
        }

        if ($this->symbol !== null) {
            $data['symbol'] = $this->symbol;
        }

        if ($this->team !== null) {
            $data['team'] = json_encode($this->team);
        }

        if ($this->royalty !== null) {
            $data['royalty'] = json_encode($this->royalty);
        }

        return $data;
    }

    public function hasImage(): bool
    {
        return $this->imageResource !== null && is_resource($this->imageResource);
    }

    public function getImageResource()
    {
        return $this->imageResource;
    }
}