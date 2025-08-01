<?php

namespace DVB\Core\SDK\DTOs;

class IpfsStatsDTO
{
    public function __construct(
        public readonly int $totalUploads,
        public readonly int $totalSize,
        public readonly int $monthlyUploads,
        public readonly int $monthlySize,
        public readonly ?string $lastUpload = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['totalUploads'] ?? 0,
            $data['totalSize'] ?? 0,
            $data['monthlyUploads'] ?? 0,
            $data['monthlySize'] ?? 0,
            $data['lastUpload'] ?? null,
        );
    }
}