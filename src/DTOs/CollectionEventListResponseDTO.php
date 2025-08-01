<?php

namespace DVB\Core\SDK\DTOs;

class CollectionEventListResponseDTO
{
    /**
     * @param int $code
     * @param string $message
     * @param CollectionEventDTO[]|null $data
     */
    public function __construct(
        public readonly int $code,
        public readonly string $message,
        public readonly ?array $data,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $events = null;
        if (isset($data['data']) && is_array($data['data'])) {
            $events = [];
            foreach ($data['data'] as $eventData) {
                $events[] = CollectionEventDTO::fromArray($eventData);
            }
        }

        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            $events,
        );
    }
}