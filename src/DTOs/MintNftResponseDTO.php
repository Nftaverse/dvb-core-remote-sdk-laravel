<?php

namespace DVB\Core\SDK\DTOs;

class MintNftResponseDTO extends ApiResponse
{
    public function __construct(int $code, string $message, mixed $data = null)
    {
        parent::__construct($code, $message, $data);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            $data['data']['remote_job_id'] ?? null,
        );
    }
}