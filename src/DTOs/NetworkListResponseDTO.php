<?php

namespace DVB\Core\SDK\DTOs;

class NetworkListResponseDTO
{
    /**
     * @param int $code
     * @param string $message
     * @param NetworkDTO[]|null $data
     */
    public function __construct(
        public readonly int $code,
        public readonly string $message,
        public readonly ?array $data,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $networks = null;
        if (isset($data['data']) && is_array($data['data'])) {
            $networks = [];
            foreach ($data['data'] as $networkData) {
                $networks[] = NetworkDTO::fromArray($networkData);
            }
        }

        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            $networks,
        );
    }
}