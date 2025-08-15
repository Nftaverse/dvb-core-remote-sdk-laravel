<?php

namespace DVB\Core\SDK\DTOs;

class PaymentDetailsResponseDTO extends ApiResponse
{
    /** @var PaymentDTO|null */
    public mixed $data;

    public function __construct(int $code, string $message, ?PaymentDTO $data = null)
    {
        parent::__construct($code, $message);
        $this->data = $data;
    }

    public static function fromArray(array $data): self
    {
        $payment = isset($data['data']['payment']) ? PaymentDTO::fromArray($data['data']['payment']) : null;

        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            $payment
        );
    }
}