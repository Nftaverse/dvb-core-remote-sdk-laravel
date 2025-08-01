<?php

namespace DVB\Core\SDK\DTOs;

class WebhookListResponseDTO
{
    /**
     * @param int $code
     * @param string $message
     * @param WebhookDTO[]|null $data
     */
    public function __construct(
        public readonly int $code,
        public readonly string $message,
        public readonly ?array $data,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $webhooks = null;
        if (isset($data['data']) && is_array($data['data'])) {
            $webhooks = [];
            foreach ($data['data'] as $webhookData) {
                $webhooks[] = WebhookDTO::fromArray($webhookData);
            }
        }

        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            $webhooks,
        );
    }
}