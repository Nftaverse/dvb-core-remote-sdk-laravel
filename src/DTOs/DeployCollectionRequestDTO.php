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
    
    /** @var resource|null */
    public $imageResource;
    
    /** @var string|null */
    public ?string $description;
    
    /** @var string|null */
    public ?string $symbol;
    
    /** @var string|null */
    public ?string $imageUrl;
    
    /** @var string|null */
    public ?string $contractMetadataUrl;
    
    /** @var string|null */
    public ?string $contractBaseUrl;
    
    /** @var array|null */
    public ?array $team;
    
    /** @var array|null */
    public ?array $roadmap;
    
    /** @var bool|null */
    public ?bool $enableOwnerSignature;
    
    /** @var int|null */
    public ?int $royalty;
    
    /** @var string|null */
    public ?string $receiveRoyaltyAddress;
    
    /** @var bool|null */
    public ?bool $enableParentContract;
    
    /** @var string|null */
    public ?string $parentContractAddress;
    
    /** @var bool|null */
    public ?bool $enableBlind;
    
    /** @var string|null */
    public ?string $blindName;
    
    /** @var string|null */
    public ?string $blindDescription;
    
    /** @var string|null */
    public ?string $blindMetadataBaseUri;
    
    /** @var resource|null */
    public $blindImageResource;
    
    /** @var string|null */
    public ?string $blindImageUrl;

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
        ?string $imageUrl = null,
        ?string $contractMetadataUrl = null,
        ?string $contractBaseUrl = null,
        ?array $team = null,
        ?array $roadmap = null,
        ?bool $enableOwnerSignature = null,
        ?int $royalty = null,
        ?string $receiveRoyaltyAddress = null,
        ?bool $enableParentContract = null,
        ?string $parentContractAddress = null,
        ?bool $enableBlind = null,
        ?string $blindName = null,
        ?string $blindDescription = null,
        ?string $blindMetadataBaseUri = null,
        $blindImageResource = null,
        ?string $blindImageUrl = null
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
        $this->imageUrl = $imageUrl;
        $this->contractMetadataUrl = $contractMetadataUrl;
        $this->contractBaseUrl = $contractBaseUrl;
        $this->team = $team;
        $this->roadmap = $roadmap;
        $this->enableOwnerSignature = $enableOwnerSignature;
        $this->royalty = $royalty;
        $this->receiveRoyaltyAddress = $receiveRoyaltyAddress;
        $this->enableParentContract = $enableParentContract;
        $this->parentContractAddress = $parentContractAddress;
        $this->enableBlind = $enableBlind;
        $this->blindName = $blindName;
        $this->blindDescription = $blindDescription;
        $this->blindMetadataBaseUri = $blindMetadataBaseUri;
        $this->blindImageResource = $blindImageResource;
        $this->blindImageUrl = $blindImageUrl;
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
        
        if ($this->imageUrl !== null) {
            $data['image_url'] = $this->imageUrl;
        }
        
        if ($this->contractMetadataUrl !== null) {
            $data['contract_metadata_url'] = $this->contractMetadataUrl;
        }
        
        if ($this->contractBaseUrl !== null) {
            $data['contract_base_url'] = $this->contractBaseUrl;
        }

        if ($this->team !== null) {
            $data['team'] = json_encode($this->team);
        }
        
        if ($this->roadmap !== null) {
            $data['roadmap'] = json_encode($this->roadmap);
        }
        
        if ($this->enableOwnerSignature !== null) {
            $data['enable_owner_signature'] = $this->enableOwnerSignature;
        }
        
        if ($this->royalty !== null) {
            $data['royalty'] = $this->royalty;
        }
        
        if ($this->receiveRoyaltyAddress !== null) {
            $data['receive_royalty_address'] = $this->receiveRoyaltyAddress;
        }
        
        if ($this->enableParentContract !== null) {
            $data['enable_parent_contract'] = $this->enableParentContract;
        }
        
        if ($this->parentContractAddress !== null) {
            $data['parent_contract_address'] = $this->parentContractAddress;
        }
        
        if ($this->enableBlind !== null) {
            $data['enable_blind'] = $this->enableBlind;
        }
        
        if ($this->blindName !== null) {
            $data['blind_name'] = $this->blindName;
        }
        
        if ($this->blindDescription !== null) {
            $data['blind_description'] = $this->blindDescription;
        }
        
        if ($this->blindMetadataBaseUri !== null) {
            $data['blind_metadata_base_uri'] = $this->blindMetadataBaseUri;
        }
        
        if ($this->blindImageUrl !== null) {
            $data['blind_image_url'] = $this->blindImageUrl;
        }

        return $data;
    }
    
    public function hasImage(): bool
    {
        return $this->imageResource !== null && is_resource($this->imageResource);
    }
    
    public function hasBlindImage(): bool
    {
        return $this->blindImageResource !== null && is_resource($this->blindImageResource);
    }
    
    public function getImageResource()
    {
        return $this->imageResource;
    }
    
    public function getBlindImageResource()
    {
        return $this->blindImageResource;
    }
}