<?php

namespace DVB\Core\SDK\DTOs;

class IpfsUploadResponseDTO
{
    public function __construct(
        public readonly int $code,
        public readonly string $message,
        public readonly ?IpfsFileDataDTO $data,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            isset($data['data']) ? IpfsFileDataDTO::fromArray($data['data']) : null,
        );
    }
}