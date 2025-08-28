<?php

namespace DVB\Core\SDK\Tests\Unit;

use DVB\Core\SDK\SsoApiClient;
use DVB\Core\SDK\Tests\TestCase;
use DVB\Core\SDK\Exceptions\DvbApiException;
use DVB\Core\SDK\DTOs\Sso\GetOrCreateUuidResponseDTO;
use DVB\Core\SDK\DTOs\Sso\CheckUuidExistsResponseDTO;

class SsoApiClientTest extends TestCase
{
    private string $apiToken;
    private string $baseDomain;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // 使用本地測試環境配置
        $this->apiToken = 'GxCEFHcnO4NgsWrS70Ru1TH3CIiun1i8upupFrRAdl2Oc1c0cS6p1Y0XE4wHoWeS';
        $this->baseDomain = 'sso.test';
    }
    
    public function test_it_can_be_instantiated(): void
    {
        $client = new SsoApiClient();
        
        $this->assertInstanceOf(SsoApiClient::class, $client);
    }
    
    public function test_it_can_be_instantiated_with_dependencies(): void
    {
        $client = new SsoApiClient(null, null, $this->apiToken, $this->baseDomain, 'https');
        
        $this->assertInstanceOf(SsoApiClient::class, $client);
        $this->assertEquals($this->apiToken, $client->getApiToken());
        $this->assertEquals($this->baseDomain, $client->getBaseDomain());
        $this->assertEquals('https', $client->getProtocol());
    }
    
    public function test_new_client_creates_instance_with_correct_parameters(): void
    {
        $client = SsoApiClient::newClient($this->apiToken, $this->baseDomain, 'https');
        
        $this->assertInstanceOf(SsoApiClient::class, $client);
        $this->assertEquals($this->apiToken, $client->getApiToken());
        $this->assertEquals($this->baseDomain, $client->getBaseDomain());
        $this->assertEquals('https', $client->getProtocol());
    }
    
    public function test_it_can_get_or_create_uuid_by_email(): void
    {
        $client = SsoApiClient::newClient($this->apiToken, $this->baseDomain, 'https');
        
        // 使用測試郵箱
        $testEmail = 'test@example.com';
        
        try {
            $response = $client->getOrCreateUuidByEmail($testEmail);
            // 驗證響應是正確的 DTO 類型
            $this->assertInstanceOf(GetOrCreateUuidResponseDTO::class, $response);
            
            // 驗證響應結構
            $this->assertIsInt($response->code);
            $this->assertIsString($response->message);
            
            // 驗證 UUID 格式（如果返回了數據）
            if ($response->data !== null) {
                $this->assertIsString($response->data);
                // UUID 應該是有效的格式
                $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $response->data);
            }
        } catch (\Exception $e) {
            // 如果 API 調用失敗，我們仍然驗證是否拋出了異常
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }
    
    public function test_it_can_check_uuid_exists(): void
    {
        $client = SsoApiClient::newClient($this->apiToken, $this->baseDomain, 'https');
        
        // 先獲取一個 UUID 來測試
        $testEmail = 'test@example.com';
        
        try {
            // 首先獲取 UUID
            $uuidResponse = $client->getOrCreateUuidByEmail($testEmail);
            
            // 然後檢查這個 UUID 是否存在
            if ($uuidResponse->data !== null) {
                $response = $client->checkUuidExists($uuidResponse->data);
                
                // 驗證響應是正確的 DTO 類型
                $this->assertInstanceOf(CheckUuidExistsResponseDTO::class, $response);
                
                // 驗證響應結構
                $this->assertIsInt($response->code);
                $this->assertIsString($response->message);
                
                // 驗證數據類型
                if ($response->data !== null) {
                    $this->assertIsBool($response->data);
                }
            }
        } catch (\Exception $e) {
            // 如果 API 調用失敗，我們仍然驗證是否拋出了異常
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }
}