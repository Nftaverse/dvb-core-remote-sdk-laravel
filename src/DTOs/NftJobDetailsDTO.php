<?php

namespace DVB\Core\SDK\DTOs;

class NftJobDetailsDTO
{
    public function __construct(
        public readonly string $jobId,
        public readonly string $jobType,
        public readonly string $status,
        public readonly ?MintNftDetailDTO $details = null,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
        public readonly ?string $completedAt = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['jobId'] ?? '',
            $data['jobType'] ?? '',
            $data['status'] ?? '',
            isset($data['details']) ? MintNftDetailDTO::fromArray($data['details']) : null,
            $data['createdAt'] ?? null,
            $data['updatedAt'] ?? null,
            $data['completedAt'] ?? null,
        );
    }
}