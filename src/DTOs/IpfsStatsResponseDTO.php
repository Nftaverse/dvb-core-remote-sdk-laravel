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
        // Handle the actual API response structure
        $stats = isset($data['user_stats']) ? IpfsStatsDTO::fromArray($data) : null;

        return new self(
            $data['code'] ?? ($data['success'] ? 200 : 0),
            $data['message'] ?? 'success',
            $stats
        );
    }
}
