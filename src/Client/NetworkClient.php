<?php

namespace DVB\Core\SDK\Client;

use DVB\Core\SDK\DTOs\NetworkListResponseDTO;
use DVB\Core\SDK\DTOs\NetworkDetailResponseDTO;

class NetworkClient extends DvbBaseClient
{
    /**
     * Get networks.
     *
     * @return \DVB\Core\SDK\DTOs\NetworkListResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getNetworks(): NetworkListResponseDTO
    {
        $response = $this->get('networks');
        return NetworkListResponseDTO::fromArray($response);
    }

    /**
     * Get network detail.
     *
     * @param int $chainId
     * @return \DVB\Core\SDK\DTOs\NetworkDetailResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getNetworkDetail(int $chainId): NetworkDetailResponseDTO
    {
        $response = $this->post('network/detail', [
            'chainId' => $chainId,
        ]);
        return NetworkDetailResponseDTO::fromArray($response);
    }
}