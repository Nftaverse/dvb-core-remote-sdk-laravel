<?php

namespace DVB\Core\SDK\DTOs;

class CheckPermissionResponseDTO extends ApiResponse
{
    /** @var bool|null */
    public mixed $data;

    public function __construct(int $code, string $message, ?bool $data = null)
    {
        parent::__construct($code, $message);
        $this->data = $data;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            $data['data']['hasPermission'] ?? null,
        );
    }
}