<?php

namespace DVB\Core\SDK\DTOs;

class NftJobDetailsResponseDTO
{
    /**
     * @param MintNftDetailResourceDTO[] $data
     */
    public function __construct(
        public readonly int $code,
        public readonly string $message,
        public readonly array $data = [],
    ) {
    }

    public static function fromArray(array $data): self
    {
        $items = [];
        if (isset($data['data']) && is_array($data['data'])) {
            foreach ($data['data'] as $item) {
                $items[] = MintNftDetailResourceDTO::fromArray($item);
            }
        }

        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            $items,
        );
    }
}