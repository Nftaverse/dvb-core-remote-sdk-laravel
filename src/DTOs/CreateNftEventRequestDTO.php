<?php

namespace DVB\Core\SDK\DTOs;

class CreateNftEventRequestDTO
{
    public function __construct(
        public readonly string $contractAddress,
        public readonly int $chainId,
        public readonly string $eventType,
        public readonly array $eventData,
        public readonly ?string $tokenId = null,
        public readonly ?string $fromAddress = null,
        public readonly ?string $toAddress = null,
        public readonly ?string $transactionHash = null,
        public readonly ?string $blockNumber = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'contractAddress' => $this->contractAddress,
            'chainId' => $this->chainId,
            'eventType' => $this->eventType,
            'eventData' => $this->eventData,
            'tokenId' => $this->tokenId,
            'fromAddress' => $this->fromAddress,
            'toAddress' => $this->toAddress,
            'transactionHash' => $this->transactionHash,
            'blockNumber' => $this->blockNumber,
        ];
    }
}