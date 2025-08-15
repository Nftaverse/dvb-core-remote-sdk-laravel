<?php

namespace DVB\Core\SDK\DTOs;

class PaymentLinkDTO
{
    public string $paymentId;
    public string $status;

    public function __construct(string $paymentId, string $status)
    {
        $this->paymentId = $paymentId;
        $this->status = $status;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['paymentId'] ?? '',
            $data['status'] ?? '',
        );
    }
}