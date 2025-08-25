<?php

namespace DVB\Core\SDK\Client;

use DVB\Core\SDK\DTOs\NftListResponseDTO;
use DVB\Core\SDK\DTOs\NftMetadataResponseDTO;
use DVB\Core\SDK\DTOs\NftJobDetailsResponseDTO;
use DVB\Core\SDK\DTOs\CheckCollectionResponseDTO;

class NftClient extends DvbBaseClient
{
    /**
     * Get NFTs by contract address.
     *
     * @param string $address
     * @param int $chainId
     * @param string|null $cursor
     * @return \DVB\Core\SDK\DTOs\NftListResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getNftsByContract(string $address, int $chainId, ?string $cursor = null): NftListResponseDTO
    {
        $query = [
            'chain_id' => $chainId,
        ];
        
        if ($cursor) {
            $query['cursor'] = $cursor;
        }

        $response = $this->get("nft/{$address}", $query);
        return NftListResponseDTO::fromArray($response);
    }

    /**
     * Get NFT metadata.
     *
     * @param string $address
     * @param string $tokenId
     * @param int $chainId
     * @return \DVB\Core\SDK\DTOs\NftMetadataResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getNftMetadata(string $address, string $tokenId, int $chainId): NftMetadataResponseDTO
    {
        $response = $this->get("nft/{$address}/{$tokenId}/metadata", [
            'chain_id' => $chainId,
        ]);
        return NftMetadataResponseDTO::fromArray($response);
    }

    /**
     * Get NFT job details.
     *
     * @param string $jobId
     * @return \DVB\Core\SDK\DTOs\NftJobDetailsResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getNftJobDetails(string $jobId): NftJobDetailsResponseDTO
    {
        $response = $this->get("nft/details/{$jobId}");
        return NftJobDetailsResponseDTO::fromArray($response);
    }

    /**
     * Create NFT event.
     *
     * @param array $data
     * @return \DVB\Core\SDK\DTOs\CheckCollectionResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function createNftEvent(array $data): CheckCollectionResponseDTO
    {
        $response = $this->post('nft/event', $data);
        return CheckCollectionResponseDTO::fromArray($response);
    }
}