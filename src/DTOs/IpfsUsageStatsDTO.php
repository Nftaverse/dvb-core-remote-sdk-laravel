<?php

namespace DVB\Core\SDK\DTOs;

class IpfsUsageStatsDTO
{
    public function __construct(
        public readonly int $total_uploads,
        public readonly int $total_storage,
        public readonly object $uploads_by_type,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['total_uploads'] ?? 0,
            $data['total_storage'] ?? 0,
            (object)($data['uploads_by_type'] ?? []),
        );
    }
}
