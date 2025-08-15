<?php

namespace DVB\Core\SDK\DTOs;

class IpfsStatsResponseDTO extends ApiResponse
{
    /** @var IpfsStatsDTO|null */
    public mixed $data;

    public function __construct(int $code, string $message, ?IpfsStatsDTO $data = null)
    {
        parent::__construct($code, $message);
        $this->data = $data;
    }

    public static function fromArray(array $data): self
    {
        $stats = isset($data['data']) ? IpfsStatsDTO::fromArray($data['data']) : null;

        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            $stats
        );
    }
}
