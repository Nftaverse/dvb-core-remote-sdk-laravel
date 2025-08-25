<?php

namespace DVB\Core\SDK\Client;

use DVB\Core\SDK\DTOs\PermissionsResponseDTO;
use DVB\Core\SDK\DTOs\CheckPermissionResponseDTO;

class PermissionClient extends DvbBaseClient
{
    /**
     * Get user permissions.
     *
     * @return \DVB\Core\SDK\DTOs\PermissionsResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getPermissions(): PermissionsResponseDTO
    {
        $response = $this->get('permission');
        return PermissionsResponseDTO::fromArray($response);
    }

    /**
     * Check if user has specific permissions.
     *
     * @param array|string $permission
     * @return \DVB\Core\SDK\DTOs\CheckPermissionResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function checkPermission(array|string $permission): CheckPermissionResponseDTO
    {
        $response = $this->post('permission/check', [
            'permission' => $permission,
        ]);
        return CheckPermissionResponseDTO::fromArray($response);
    }
}