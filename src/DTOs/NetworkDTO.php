<?php

namespace DVB\Core\SDK\DTOs;

class NetworkDTO
{
    public int $chainId;
    public string $name;
    public ?string $symbol;
    public ?int $decimals;
    public ?string $rpcUrl;
    public ?string $explorerUrl;

    public function __construct(
        int $chainId,
        string $name,
        ?string $symbol = null,
        ?int $decimals = null,
        ?string $rpcUrl = null,
        ?string $explorerUrl = null
    ) {
        $this->chainId = $chainId;
        $this->name = $name;
        $this->symbol = $symbol;
        $this->decimals = $decimals;
        $this->rpcUrl = $rpcUrl;
        $this->explorerUrl = $explorerUrl;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['chainId'] ?? $data['chain_id'] ?? 0,
            $data['name'] ?? '',
            $data['symbol'] ?? null,
            isset($data['decimals']) ? (int)$data['decimals'] : null,
            $data['rpcUrl'] ?? $data['rpc_url'] ?? null,
            $data['explorerUrl'] ?? $data['explorer_url'] ?? null,
        );
    }
}