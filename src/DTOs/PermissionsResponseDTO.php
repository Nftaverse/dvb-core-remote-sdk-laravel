<?php

namespace DVB\Core\SDK\DTOs;

class PermissionsResponseDTO
{
    /**
     * @param int $code
     * @param string $message
     * @param PermissionDTO[]|null $data
     */
    public function __construct(
        public readonly int $code,
        public readonly string $message,
        public readonly ?array $data,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $permissions = null;
        if (isset($data['data']) && is_array($data['data'])) {
            $permissions = [];
            foreach ($data['data'] as $permissionData) {
                $permissions[] = PermissionDTO::fromArray($permissionData);
            }
        }

        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            $permissions,
        );
    }
}