<?php

namespace DVB\Core\SDK\DTOs;

class PaymentGatewayResponseDTO extends ApiResponse
{
    /** @var PaymentGatewayInfoDTO|null */
    public mixed $data;

    public function __construct(int $code, string $message, ?PaymentGatewayInfoDTO $data = null)
    {
        parent::__construct($code, $message);
        $this->data = $data;
    }

    public static function fromArray(array $data): self
    {
        $gateway = isset($data['data']['gateway']) ? PaymentGatewayInfoDTO::fromArray($data['data']['gateway']) : null;

        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            $gateway
        );
    }
}