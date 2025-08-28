<?php

namespace DVB\Core\SDK\DTOs\Sso;

class CheckUuidExistsResponseDTO extends \DVB\Core\SDK\DTOs\ApiResponse
{
    /** @var bool|null */
    public mixed $data;

    public function __construct(int $code, string $message, ?bool $data = null)
    {
        parent::__construct($code, $message);
        $this->data = $data;
    }

    public static function fromArray(array $data): self
    {
        // 處理可能的數據結構
        // 假設返回格式為: {"success": true, "exists": true} 或 {"success": true, "data": true}
        $code = $data['success'] ?? false ? 200 : 500;
        $message = $data['success'] ?? false ? 'Success' : 'Failed';
        $exists = null;
        
        // 檢查不同的可能字段
        if (isset($data['exists'])) {
            $exists = (bool) $data['exists'];
        } elseif (isset($data['data'])) {
            $exists = (bool) $data['data'];
        }
        
        return new self($code, $message, $exists);
    }
}