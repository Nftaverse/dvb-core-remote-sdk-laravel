<?php

namespace DVB\Core\SDK\DTOs;

class MintNftDetailDTO
{
    public function __construct(
        public readonly string $contractAddress,
        public readonly int $chainId,
        public readonly string $tokenId,
        public readonly string $toAddress,
        public readonly string $tokenUri,
        public readonly ?string $transactionHash = null,
        public readonly ?string $blockNumber = null,
        public readonly ?string $status = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['contractAddress'] ?? '',
            $data['chainId'] ?? 0,
            $data['tokenId'] ?? '',
            $data['toAddress'] ?? '',
            $data['tokenUri'] ?? '',
            $data['transactionHash'] ?? null,
            $data['blockNumber'] ?? null,
            $data['status'] ?? null,
        );
    }
}