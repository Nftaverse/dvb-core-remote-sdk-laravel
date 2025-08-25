<?php

namespace DVB\Core\SDK\Client;

use DVB\Core\SDK\DTOs\ApiResponse;

class CommunicationClient extends DvbBaseClient
{
    /**
     * Send email to user.
     *
     * @param string $email
     * @param string $subject
     * @param string $body
     * @return \DVB\Core\SDK\DTOs\ApiResponse
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function sendEmail(string $email, string $subject, string $body): ApiResponse
    {
        $response = $this->post('send-email', [
            'email' => $email,
            'subject' => $subject,
            'body' => $body,
        ]);
        return ApiResponse::fromArray($response);
    }

    /**
     * Send SMS to user.
     *
     * @param string $phone
     * @param string $body
     * @return \DVB\Core\SDK\DTOs\ApiResponse
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function sendSms(string $phone, string $body): ApiResponse
    {
        $response = $this->post('send-sms', [
            'phone' => $phone,
            'body' => $body,
        ]);
        return ApiResponse::fromArray($response);
    }
}