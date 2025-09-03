<?php

namespace DVB\Core\SDK\DTOs;

class MintNftRequestDTO
{
    /** @var int */
    public int $chainId;
    
    /** @var string */
    public string $address;
    
    /** @var string */
    public string $toAddress;
    
    /** @var int */
    public int $amount;
    
    /** @var string|null */
    public ?string $reference;
    
    /** @var string|null */
    public ?string $metadata;

    public function __construct(
        int $chainId,
        string $address,
        string $toAddress,
        int $amount,
        ?string $reference = null,
        ?string $metadata = null
    ) {
        $this->chainId = $chainId;
        $this->address = $address;
        $this->toAddress = $toAddress;
        $this->amount = $amount;
        $this->reference = $reference;
        $this->metadata = $metadata;
    }

    public function toArray(): array
    {
        $data = [
            'chain_id' => $this->chainId,
            'address' => $this->address,
            'to_address' => $this->toAddress,
            'amount' => $this->amount,
        ];

        if ($this->reference !== null) {
            $data['reference'] = $this->reference;
        }

        if ($this->metadata !== null) {
            $data['metadata'] = $this->metadata;
        }

        return $data;
    }
}