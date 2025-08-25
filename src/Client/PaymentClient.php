<?php

namespace DVB\Core\SDK\Client;

use DVB\Core\SDK\DTOs\CreatePaymentResponseDTO;
use DVB\Core\SDK\DTOs\PaymentDetailsResponseDTO;
use DVB\Core\SDK\DTOs\PaymentGatewayResponseDTO;
use DVB\Core\SDK\DTOs\PaymentMethodListResponseDTO;

class PaymentClient extends DvbBaseClient
{
    /**
     * Create payment request.
     *
     * @param array $data
     * @return \DVB\Core\SDK\DTOs\CreatePaymentResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function createPaymentRequest(array $data): CreatePaymentResponseDTO
    {
        $response = $this->post('payment-requests', $data);
        return CreatePaymentResponseDTO::fromArray($response);
    }

    /**
     * Get payment request by ID.
     *
     * @param string $id
     * @return \DVB\Core\SDK\DTOs\PaymentDetailsResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getPaymentRequest(string $id): PaymentDetailsResponseDTO
    {
        $response = $this->get("payment-requests/{$id}");
        return PaymentDetailsResponseDTO::fromArray($response);
    }

    /**
     * Get payment gateway by ID.
     *
     * @param string $id
     * @return \DVB\Core\SDK\DTOs\PaymentGatewayResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getPaymentGateway(string $id): PaymentGatewayResponseDTO
    {
        $response = $this->get("payment-gateways/{$id}");
        return PaymentGatewayResponseDTO::fromArray($response);
    }

    /**
     * Get payment methods.
     *
     * @return \DVB\Core\SDK\DTOs\PaymentMethodListResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getPaymentMethods(): PaymentMethodListResponseDTO
    {
        $response = $this->get('payment-method');
        return PaymentMethodListResponseDTO::fromArray($response);
    }
}