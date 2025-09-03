<?php

namespace DVB\Core\SDK\Tests\Integration;

use DVB\Core\SDK\DTOs\DeployCollectionRequestDTO;

class CollectionClientIntegrationTest extends IntegrationTestCase
{
    public function test_deploy_collection_with_real_api(): void
    {
        // 只有在集成測試啟用時才運行
        if (!self::isIntegrationTestEnabled()) {
            $this->markTestSkipped('Integration test requires DVB_API_KEY and DVB_API_DOMAIN environment variables');
        }

        // 創建一個臨時的 PNG 文件
        $tempFile = tempnam(sys_get_temp_dir(), 'test_image') . '.png';
        $imageData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH4QgLEw4JtO25LwAAABl0RVh0Q29tbWVudABDcmVhdGVkIHdpdGggR0lNUFeBDhcAAABnSURBVDjLY2CgK2D4//8/AyUYA8bGxv8LCgr+41MDFM/PL2DAoQHGYGRkZPj37x8DFG9oaPiflpb2H6+a0tJSBjQNYWFh/+Pi4v7jVBMWFvY/MTHxP141KSkp/1NSUv7jVZOSkvI/JibmP141DAwAEC4MF6Hc2GwAAAAASUVORK5CYII=');
        file_put_contents($tempFile, $imageData);
        $imageResource = fopen($tempFile, 'rb');

        // 創建 DeployCollectionRequestDTO
        $request = new DeployCollectionRequestDTO(
            chainId: 1, // Ethereum mainnet
            ownerAddress: '0x1234567890123456789012345678901234567890', // 測試地址
            name: 'Test Collection ' . time(), // 添加時間戳以避免重複名稱
            quantity: 100,
            enableFlexibleMint: true,
            enableSoulbound: false,
            imageResource: $imageResource,
            description: 'Test collection description',
            symbol: 'TEST'
        );

        // 調用 deployCollection 方法
        try {
            $response = $this->getClient()->deployCollection($request);
            
            // 驗證響應
            $this->assertNotNull($response);
            $this->assertIsNumeric($response->code);
        } catch (\Exception $e) {
            echo "Exception: " . $e->getMessage() . "\n";
            throw $e;
        }
        
        // 清理資源
        // 注意：圖片資源可能在請求發送後已經被 Guzzle 關閉，所以我們只刪除臨時文件
        if (isset($tempFile) && file_exists($tempFile)) {
            unlink($tempFile);
        }
    }
}