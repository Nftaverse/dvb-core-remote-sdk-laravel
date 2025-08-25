<?php

namespace DVB\Core\SDK\Client;

use DVB\Core\SDK\Enums\WebhookType;
use DVB\Core\SDK\DTOs\WebhookListResponseDTO;
use DVB\Core\SDK\DTOs\WebhookDetailsResponseDTO;

class WebhookClient extends DvbBaseClient
{
    /**
     * Get webhooks.
     *
     * @return \DVB\Core\SDK\DTOs\WebhookListResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getWebhooks(): WebhookListResponseDTO
    {
        $response = $this->get('webhook');
        return WebhookListResponseDTO::fromArray($response);
    }

    /**
     * Create webhook.
     *
     * @param string $url
     * @param \DVB\Core\SDK\Enums\WebhookType $type
     * @param string|null $name
     * @param string|null $collectionAddress
     * @param string|null $collectionChainId
     * @return \DVB\Core\SDK\DTOs\WebhookListResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function createWebhook(string $url, WebhookType $type, ?string $name = null, ?string $collectionAddress = null, ?string $collectionChainId = null): WebhookListResponseDTO
    {
        $data = [
            'url' => $url,
            'type' => $type->value,
        ];

        if ($name) {
            $data['name'] = $name;
        }

        if ($collectionAddress) {
            $data['collectionAddress'] = $collectionAddress;
        }

        if ($collectionChainId) {
            $data['collectionChainId'] = $collectionChainId;
        }
        
        // Validate that collection address and chain ID are provided for types that require them
        if (in_array($type, [WebhookType::mint_nft, WebhookType::transfer_nft], true) && (empty($collectionAddress) || empty($collectionChainId))) {
            throw new \InvalidArgumentException('Collection address and chain ID are required for this webhook type.');
        }

        $response = $this->post('webhook', $data);
        return WebhookListResponseDTO::fromArray($response);
    }

    /**
     * Get webhook by ID.
     *
     * @param string $id
     * @return \DVB\Core\SDK\DTOs\WebhookDetailsResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getWebhook(string $id): WebhookDetailsResponseDTO
    {
        $response = $this->get("webhook/{$id}");
        return WebhookDetailsResponseDTO::fromArray($response);
    }

    /**
     * Delete webhook by ID.
     *
     * @param string $id
     * @return \DVB\Core\SDK\DTOs\WebhookDetailsResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function deleteWebhook(string $id): WebhookDetailsResponseDTO
    {
        $response = $this->delete("webhook/{$id}");
        return WebhookDetailsResponseDTO::fromArray($response);
    }
}