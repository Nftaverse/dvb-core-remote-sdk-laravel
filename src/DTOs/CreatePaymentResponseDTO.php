<?php

namespace DVB\Core\SDK\DTOs;

class CreatePaymentResponseDTO extends ApiResponse
{
    /** @var PaymentLinkDTO|null */
    public mixed $data;

    public function __construct(int $code, string $message, ?PaymentLinkDTO $data = null)
    {
        parent::__construct($code, $message);
        $this->data = $data;
    }

    public static function fromArray(array $data): self
    {
        $paymentLink = isset($data['data']['paymentLink']) ? PaymentLinkDTO::fromArray($data['data']['paymentLink']) : null;

        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            $paymentLink
        );
    }
}