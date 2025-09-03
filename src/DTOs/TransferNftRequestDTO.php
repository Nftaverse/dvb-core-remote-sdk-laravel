<?php

namespace DVB\Core\SDK\DTOs;

class TransferNftRequestDTO
{
    /** @var int */
    public int $chainId;
    
    /** @var string */
    public string $toAddress;

    public function __construct(int $chainId, string $toAddress)
    {
        $this->chainId = $chainId;
        $this->toAddress = $toAddress;
    }

    public function toArray(): array
    {
        return [
            'chain_id' => $this->chainId,
            'to_address' => $this->toAddress,
        ];
    }
}