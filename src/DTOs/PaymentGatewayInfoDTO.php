<?php

namespace DVB\Core\SDK\DTOs;

class PaymentGatewayInfoDTO
{
    public string $gatewayId;
    public string $name;

    public function __construct(string $gatewayId, string $name)
    {
        $this->gatewayId = $gatewayId;
        $this->name = $name;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['gatewayId'] ?? '',
            $data['name'] ?? '',
        );
    }
}