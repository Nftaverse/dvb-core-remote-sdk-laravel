<?php

namespace DVB\Core\SDK\DTOs;

class PermissionsResponseDTO
{
    public int $code;
    public string $message;
    /** @var string[]|PermissionDTO[]|null */
    public ?array $data;

    /**
     * @param int $code
     * @param string $message
     * @param string[]|PermissionDTO[]|null $data
     */
    public function __construct(int $code, string $message, ?array $data)
    {
        $this->code = $code;
        $this->message = $message;
        $this->data = $data;
    }

    public static function fromArray(array $data): self
    {
        $permissions = null;
        if (isset($data['data']['permissions']) && is_array($data['data']['permissions'])) {
            $permissions = [];
            foreach ($data['data']['permissions'] as $permissionData) {
                if (is_array($permissionData)) {
                    // If it's an array, assume it's a PermissionDTO data array
                    $permissions[] = PermissionDTO::fromArray($permissionData);
                } else {
                    // If it's not an array, keep it as is (e.g., string)
                    $permissions[] = $permissionData;
                }
            }
        }

        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            $permissions,
        );
    }
}