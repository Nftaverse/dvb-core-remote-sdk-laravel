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
        // 檢查是否有圖片資源
        if ($request->hasImage() || $request->hasBlindImage()) {
            // 使用 multipart 格式上傳圖片和數據
            $data = $request->toArray();
            $multipart = [];
            
            // 添加所有字段
            foreach ($data as $key => $value) {
                $multipart[] = [
                    'name' => $key,
                    'contents' => $value
                ];
            }
            
            // 如果有圖片資源，替換對應的字段
            if ($request->hasImage()) {
                // 移除 image_url 字段（如果存在）
                $multipart = array_filter($multipart, function($item) {
                    return $item['name'] !== 'image_url';
                });
                
                // 添加圖片資源
                $multipart[] = [
                    'name' => 'image',
                    'contents' => $request->getImageResource()
                ];
            }
            
            // 如果有盲盒圖片資源，替換對應的字段
            if ($request->hasBlindImage()) {
                // 移除 blind_image_url 字段（如果存在）
                $multipart = array_filter($multipart, function($item) {
                    return $item['name'] !== 'blind_image_url';
                });
                
                // 添加盲盒圖片資源
                $multipart[] = [
                    'name' => 'blind_image',
                    'contents' => $request->getBlindImageResource()
                ];
            }
            
            // 重新索引數組
            $multipart = array_values($multipart);
            
            // 調試輸出
            echo "Multipart data:\n";
            foreach ($multipart as $item) {
                echo "  - {$item['name']}: " . (is_resource($item['contents']) ? '[RESOURCE]' : $item['contents']) . "\n";
            }
            
            $response = $this->request('POST', 'collection', [
                'multipart' => $multipart
            ]);
        } else {
            // 如果沒有圖片資源，使用 form_params
            $data = $request->toArray();
            
            // 調試輸出
            echo "Form params data:\n";
            foreach ($data as $key => $value) {
                echo "  - {$key}: " . (is_resource($value) ? '[RESOURCE]' : $value) . "\n";
            }
            
            // 再次檢查布爾值是否正確轉換
            foreach ($data as $key => $value) {
                if (is_bool($value)) {
                    echo "WARNING: Boolean value found in form data: {$key} = " . ($value ? 'true' : 'false') . "\n";
                }
            }
            
            $response = $this->postFormData('collection', $data);
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