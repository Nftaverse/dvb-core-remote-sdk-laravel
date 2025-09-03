<?php

namespace DVB\Core\SDK\DTOs;

class DeployCollectionResponseDTO extends ApiResponse
{
    public function __construct(int $code, string $message, ?string $data = null)
    {
        parent::__construct($code, $message, $data);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            $data['data']['launchpad_id'] ?? null,
        );
    }
}