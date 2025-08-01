<?php

namespace DVB\Core\SDK\DTOs;

class IpfsFileDataDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $hash,
        public readonly string $size,
        public readonly ?string $url = null,
        public readonly ?string $cdnUrl = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'] ?? '',
            $data['hash'] ?? '',
            $data['size'] ?? '',
            $data['url'] ?? null,
            $data['cdnUrl'] ?? null,
        );
    }
}