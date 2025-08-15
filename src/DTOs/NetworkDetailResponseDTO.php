<?php

namespace DVB\Core\SDK\DTOs;

class NetworkDetailResponseDTO extends ApiResponse
{
    /** @var NetworkDTO|null */
    public mixed $data;

    public function __construct(int $code, string $message, ?NetworkDTO $data = null)
    {
        parent::__construct($code, $message);
        $this->data = $data;
    }

    public static function fromArray(array $data): self
    {
        $network = isset($data['data']['network']) ? NetworkDTO::fromArray($data['data']['network']) : null;

        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            $network
        );
    }
}