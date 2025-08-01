<?php

namespace DVB\Core\SDK\DTOs;

class IpfsStatsResponseDTO
{
    public function __construct(
        public readonly int $code,
        public readonly string $message,
        public readonly ?IpfsStatsDTO $data,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            isset($data['data']) ? IpfsStatsDTO::fromArray($data['data']) : null,
        );
    }
}