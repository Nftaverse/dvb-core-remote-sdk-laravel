<?php

namespace DVB\Core\SDK\DTOs;

class CollectionEventDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $collectionAddress,
        public readonly int $chainId,
        public readonly string $eventType,
        public readonly ?string $tokenId = null,
        public readonly ?string $fromAddress = null,
        public readonly ?string $toAddress = null,
        public readonly ?string $transactionHash = null,
        public readonly ?string $blockNumber = null,
        public readonly ?string $timestamp = null,
    ) {
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