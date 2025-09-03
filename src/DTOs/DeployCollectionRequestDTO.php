<?php

namespace DVB\Core\SDK\DTOs;

class DeployCollectionRequestDTO
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
    
    /** @var string|null */
    public ?string $description;
    
    /** @var string|null */
    public ?string $symbol;
    
    /** @var string|null */
    public ?string $image;
    
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
        ?string $description = null,
        ?string $symbol = null,
        ?string $image = null,
        ?array $team = null,
        ?array $royalty = null
    ) {
        $this->chainId = $chainId;
        $this->ownerAddress = $ownerAddress;
        $this->name = $name;
        $this->quantity = $quantity;
        $this->enableFlexibleMint = $enableFlexibleMint;
        $this->enableSoulbound = $enableSoulbound;
        $this->description = $description;
        $this->symbol = $symbol;
        $this->image = $image;
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

        if ($this->image !== null) {
            $data['image'] = $this->image;
        }

        if ($this->team !== null) {
            $data['team'] = json_encode($this->team);
        }

        if ($this->royalty !== null) {
            $data['royalty'] = json_encode($this->royalty);
        }

        return $data;
    }
}