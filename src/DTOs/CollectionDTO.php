<?php

namespace DVB\Core\SDK\DTOs;

class CollectionDTO
{
    public int $chainId;
    public string $address;
    public string $name;
    public ?string $description;
    public string $symbol;
    public int $decimals;
    public int $totalSupply;
    public ?string $logo;
    public float $royalty;
    public string $contractType;
    public bool $isFlexibleMint;
    public bool $isJcd;
    public ?string $launchpadId;
    public int $createdAt;
    public int $updatedAt;

    public function __construct(
        int $chainId,
        string $address,
        string $name,
        ?string $description,
        string $symbol,
        int $decimals,
        int $totalSupply,
        ?string $logo,
        float $royalty,
        string $contractType,
        bool $isFlexibleMint,
        bool $isJcd,
        ?string $launchpadId,
        int $createdAt,
        int $updatedAt
    ) {
        $this->chainId = $chainId;
        $this->address = $address;
        $this->name = $name;
        $this->description = $description;
        $this->symbol = $symbol;
        $this->decimals = $decimals;
        $this->totalSupply = $totalSupply;
        $this->logo = $logo;
        $this->royalty = $royalty;
        $this->contractType = $contractType;
        $this->isFlexibleMint = $isFlexibleMint;
        $this->isJcd = $isJcd;
        $this->launchpadId = $launchpadId;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['chain_id'] ?? 0,
            $data['address'] ?? '',
            $data['name'] ?? '',
            $data['description'] ?? null,
            $data['symbol'] ?? '',
            $data['decimals'] ?? 0,
            $data['total_supply'] ?? 0,
            $data['logo'] ?? null,
            $data['royalty'] ?? 0.0,
            $data['contract_type'] ?? '',
            $data['is_flexible_mint'] ?? false,
            $data['is_jcd'] ?? false,
            $data['launchpad_id'] ?? null,
            $data['created_at'] ?? 0,
            $data['updated_at'] ?? 0,
        );
    }
}