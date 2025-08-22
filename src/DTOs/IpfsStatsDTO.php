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
        // Handle the actual API response structure
        $userData = $data['user_stats'] ?? $data;
        return new self(
            $userData['total_uploads'] ?? 0,
            $userData['total_storage'] ?? 0
        );
    }
}
