<?php

namespace DVB\Core\SDK\Client;

use DVB\Core\SDK\DTOs\CollectionListResponseDTO;
use DVB\Core\SDK\DTOs\CollectionEventListResponseDTO;
use DVB\Core\SDK\DTOs\CheckCollectionResponseDTO;
use DVB\Core\SDK\DTOs\DeployCollectionRequestDTO;
use DVB\Core\SDK\DTOs\DeployCollectionResponseDTO;
use DVB\Core\SDK\DTOs\CollectionDetailResponseDTO;
use DVB\Core\SDK\DTOs\MintNftRequestDTO;
use DVB\Core\SDK\DTOs\MintNftResponseDTO;

class CollectionClient extends DvbBaseClient
{
    /**
     * Get collections list.
     *
     * @param int $chainId
     * @param string|null $cursor
     * @return \DVB\Core\SDK\DTOs\CollectionListResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getCollections(int $chainId, ?string $cursor = null): CollectionListResponseDTO
    {
        $query = [
            'chain_id' => $chainId,
        ];
        
        if ($cursor) {
            $query['cursor'] = $cursor;
        }

        $response = $this->get('collection', $query);
        return CollectionListResponseDTO::fromArray($response);
    }

    /**
     * Get owned collections list.
     *
     * @param int|null $chainId
     * @param string|null $cursor
     * @return \DVB\Core\SDK\DTOs\CollectionListResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getOwnCollections(?int $chainId = null, ?string $cursor = null): CollectionListResponseDTO
    {
        $query = [];
        
        if ($chainId !== null) {
            $query['chain_id'] = $chainId;
        }
        
        if ($cursor) {
            $query['cursor'] = $cursor;
        }

        $response = $this->get('collection/own', $query);
        return CollectionListResponseDTO::fromArray($response);
    }

    /**
     * Get collection events.
     *
     * @param string $address
     * @param int $chainId
     * @return \DVB\Core\SDK\DTOs\CollectionEventListResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getCollectionEvents(string $address, int $chainId): CollectionEventListResponseDTO
    {
        $response = $this->get("collection/{$address}/event", [
            'chain_id' => $chainId,
        ]);
        return CollectionEventListResponseDTO::fromArray($response);
    }

    /**
     * Check collection.
     *
     * @param int $chainId
     * @param string $address
     * @param string $toAddress
     * @return \DVB\Core\SDK\DTOs\CheckCollectionResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function checkCollection(int $chainId, string $address, string $toAddress): CheckCollectionResponseDTO
    {
        $response = $this->post('collection/check', [], [
            'chain_id' => $chainId,
            'address' => $address,
            'to_address' => $toAddress,
        ]);
        return CheckCollectionResponseDTO::fromArray($response);
    }

    /**
     * Deploy a new collection.
     *
     * @param \DVB\Core\SDK\DTOs\DeployCollectionRequestDTO $request
     * @return \DVB\Core\SDK\DTOs\DeployCollectionResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function deployCollection(DeployCollectionRequestDTO $request): DeployCollectionResponseDTO
    {
        // 檢查是否為包含圖片的請求
        if (method_exists($request, 'hasImage') && $request->hasImage()) {
            // 使用 multipart 格式上傳圖片和數據
            $data = $request->toArray();
            $multipart = [];
            
            // 添加所有表單數據
            foreach ($data as $key => $value) {
                $multipart[] = [
                    'name' => $key,
                    'contents' => $value
                ];
            }
            
            // 添加圖片資源
            $multipart[] = [
                'name' => 'image',
                'contents' => $request->getImageResource()
            ];
            
            $response = $this->request('POST', 'collection', [
                'multipart' => $multipart
            ]);
        } else {
            // 驗證是否需要圖片資源
            if (property_exists($request, 'imageResource') && $request->imageResource === null) {
                throw new \InvalidArgumentException('Image resource is required for collection deployment');
            }
            
            $response = $this->post('collection', [], $request->toArray());
        }
        
        return DeployCollectionResponseDTO::fromArray($response);
    }

    /**
     * Get collection details.
     *
     * @param string $address
     * @param int $chainId
     * @return \DVB\Core\SDK\DTOs\CollectionDetailResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getCollectionDetails(string $address, int $chainId): CollectionDetailResponseDTO
    {
        $response = $this->get("collection/{$address}", [
            'chain_id' => $chainId,
        ]);
        return CollectionDetailResponseDTO::fromArray($response);
    }

    /**
     * Mint NFT in collection.
     *
     * @param \DVB\Core\SDK\DTOs\MintNftRequestDTO $request
     * @return \DVB\Core\SDK\DTOs\MintNftResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function mintNft(MintNftRequestDTO $request): MintNftResponseDTO
    {
        $response = $this->post('collection/mint-nft', [], $request->toArray());
        return MintNftResponseDTO::fromArray($response);
    }
}