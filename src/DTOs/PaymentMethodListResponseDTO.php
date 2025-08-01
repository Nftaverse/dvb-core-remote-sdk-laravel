<?php

namespace DVB\Core\SDK\DTOs;

class PaymentMethodListResponseDTO
{
    /**
     * @param int $code
     * @param string $message
     * @param PaymentMethodDTO[]|null $data
     */
    public function __construct(
        public readonly int $code,
        public readonly string $message,
        public readonly ?array $data,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $methods = null;
        if (isset($data['data']) && is_array($data['data'])) {
            $methods = [];
            foreach ($data['data'] as $methodData) {
                $methods[] = PaymentMethodDTO::fromArray($methodData);
            }
        }

        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            $methods,
        );
    }
}