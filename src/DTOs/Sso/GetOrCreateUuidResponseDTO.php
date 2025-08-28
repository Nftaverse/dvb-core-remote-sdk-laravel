<?php

namespace DVB\Core\SDK\DTOs\Sso;

class GetOrCreateUuidResponseDTO extends \DVB\Core\SDK\DTOs\ApiResponse
{
    /** @var string|null */
    public mixed $data;

    public function __construct(int $code, string $message, ?string $data = null)
    {
        parent::__construct($code, $message);
        $this->data = $data;
    }

    public static function fromArray(array $data): self
    {
        // 處理新的數據結構: {"success": true, "uuid": "01K3JP5R2BK8QM2V5TZF5XJ854"}
        $code = $data['success'] ?? false ? 200 : 500;
        $message = $data['success'] ?? false ? 'Success' : 'Failed';
        $uuid = $data['uuid'] ?? null;
        
        return new self($code, $message, $uuid);
    }
}