<?php

namespace DVB\Core\SDK\DTOs;

class IpfsStatsDTO
{
    public int $totalUploads;
    public int $totalSize;

    public function __construct(int $totalUploads, int $totalSize)
    {
        $this->totalUploads = $totalUploads;
        $this->totalSize = $totalSize;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['totalUploads'] ?? 0,
            $data['totalSize'] ?? 0
        );
    }
}
