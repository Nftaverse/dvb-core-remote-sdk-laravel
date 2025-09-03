<?php

namespace DVB\Core\SDK\DTOs;

class CollectionDetailResponseDTO extends ApiResponse
{
    /** @var array|null */
    public ?array $data;

    public function __construct(int $code, string $message, ?array $data = null)
    {
        parent::__construct($code, $message);
        $this->data = $data;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            $data['data'] ?? null,
        );
    }
}