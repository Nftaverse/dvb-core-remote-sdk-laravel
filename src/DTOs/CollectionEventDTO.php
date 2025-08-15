<?php

namespace DVB\Core\SDK\DTOs;

class CollectionEventDTO
{
    public string $id;
    public string $collectionAddress;
    public int $chainId;
    public string $eventType;
    public ?string $tokenId;
    public ?string $fromAddress;
    public ?string $toAddress;
    public ?string $transactionHash;
    public ?string $blockNumber;
    public ?string $timestamp;

    public function __construct(
        string $id,
        string $collectionAddress,
        int $chainId,
        string $eventType,
        ?string $tokenId = null,
        ?string $fromAddress = null,
        ?string $toAddress = null,
        ?string $transactionHash = null,
        ?string $blockNumber = null,
        ?string $timestamp = null
    ) {
        $this->id = $id;
        $this->collectionAddress = $collectionAddress;
        $this->chainId = $chainId;
        $this->eventType = $eventType;
        $this->tokenId = $tokenId;
        $this->fromAddress = $fromAddress;
        $this->toAddress = $toAddress;
        $this->transactionHash = $transactionHash;
        $this->blockNumber = $blockNumber;
        $this->timestamp = $timestamp;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? '',
            $data['collectionAddress'] ?? '',
            $data['chainId'] ?? 0,
            $data['eventType'] ?? '',
            $data['tokenId'] ?? null,
            $data['fromAddress'] ?? null,
            $data['toAddress'] ?? null,
            $data['transactionHash'] ?? null,
            $data['blockNumber'] ?? null,
            $data['timestamp'] ?? null,
        );
    }
}