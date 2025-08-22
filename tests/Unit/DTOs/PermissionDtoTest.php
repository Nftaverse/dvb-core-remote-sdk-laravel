<?php

namespace DVB\Core\SDK\Tests\Unit\DTOs;

use DVB\Core\SDK\DTOs\PermissionDTO;
use DVB\Core\SDK\DTOs\PermissionsResponseDTO;
use DVB\Core\SDK\DTOs\CheckPermissionResponseDTO;
use DVB\Core\SDK\Tests\TestCase;

class PermissionDtoTest extends TestCase
{
    public function test_permission_dto_can_be_created_from_array()
    {
        $data = [
            'id' => 'perm123',
            'name' => 'Read Permission',
            'description' => 'Allows reading data',
            'createdAt' => '2023-01-01T00:00:00Z',
        ];

        $permission = PermissionDTO::fromArray($data);

        $this->assertInstanceOf(PermissionDTO::class, $permission);
        $this->assertEquals('perm123', $permission->id);
        $this->assertEquals('Read Permission', $permission->name);
        $this->assertEquals('Allows reading data', $permission->description);
        $this->assertEquals('2023-01-01T00:00:00Z', $permission->createdAt);
    }

    public function test_permission_dto_can_be_created_with_missing_optional_fields()
    {
        $data = [
            'id' => 'perm123',
            'name' => 'Read Permission',
        ];

        $permission = PermissionDTO::fromArray($data);

        $this->assertInstanceOf(PermissionDTO::class, $permission);
        $this->assertEquals('perm123', $permission->id);
        $this->assertEquals('Read Permission', $permission->name);
        $this->assertNull($permission->description);
        $this->assertNull($permission->createdAt);
    }

    public function test_permission_dto_can_be_created_with_null_values()
    {
        $data = [
            'id' => 'perm123',
            'name' => 'Read Permission',
            'description' => null,
            'createdAt' => null,
        ];

        $permission = PermissionDTO::fromArray($data);

        $this->assertInstanceOf(PermissionDTO::class, $permission);
        $this->assertEquals('perm123', $permission->id);
        $this->assertEquals('Read Permission', $permission->name);
        $this->assertNull($permission->description);
        $this->assertNull($permission->createdAt);
    }

    public function test_permissions_response_dto_can_be_created_from_array()
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'permissions' => [
                    [
                        'id' => 'perm123',
                        'name' => 'Read Permission',
                        'description' => 'Allows reading data',
                    ],
                    [
                        'id' => 'perm456',
                        'name' => 'Write Permission',
                        'description' => 'Allows writing data',
                    ]
                ]
            ],
        ];

        $response = PermissionsResponseDTO::fromArray($data);

        $this->assertInstanceOf(PermissionsResponseDTO::class, $response);
        $this->assertEquals(200, $response->code);
        $this->assertEquals('Success', $response->message);
        $this->assertIsArray($response->data);
        $this->assertCount(2, $response->data);
        $this->assertInstanceOf(PermissionDTO::class, $response->data[0]);
        $this->assertEquals('perm123', $response->data[0]->id);
    }

    public function test_permissions_response_dto_can_handle_empty_permissions()
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'permissions' => []
            ],
        ];

        $response = PermissionsResponseDTO::fromArray($data);

        $this->assertInstanceOf(PermissionsResponseDTO::class, $response);
        $this->assertEquals(200, $response->code);
        $this->assertEquals('Success', $response->message);
        $this->assertIsArray($response->data);
        $this->assertCount(0, $response->data);
    }

    public function test_permissions_response_dto_can_handle_null_data()
    {
        $data = [
            'code' => 404,
            'message' => 'Permissions not found',
            'data' => null,
        ];

        $response = PermissionsResponseDTO::fromArray($data);

        $this->assertInstanceOf(PermissionsResponseDTO::class, $response);
        $this->assertEquals(404, $response->code);
        $this->assertEquals('Permissions not found', $response->message);
        $this->assertNull($response->data);
    }

    public function test_check_permission_response_dto_can_be_created_from_array()
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'hasPermission' => true
            ],
        ];

        $response = CheckPermissionResponseDTO::fromArray($data);

        $this->assertInstanceOf(CheckPermissionResponseDTO::class, $response);
        $this->assertEquals(200, $response->code);
        $this->assertEquals('Success', $response->message);
        $this->assertTrue($response->data);
    }

    public function test_check_permission_response_dto_handles_false_permission()
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'hasPermission' => false
            ],
        ];

        $response = CheckPermissionResponseDTO::fromArray($data);

        $this->assertInstanceOf(CheckPermissionResponseDTO::class, $response);
        $this->assertEquals(200, $response->code);
        $this->assertEquals('Success', $response->message);
        $this->assertFalse($response->data);
    }

    public function test_check_permission_response_dto_handles_missing_permission_field()
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'data' => [],
        ];

        $response = CheckPermissionResponseDTO::fromArray($data);

        $this->assertInstanceOf(CheckPermissionResponseDTO::class, $response);
        $this->assertEquals(200, $response->code);
        $this->assertEquals('Success', $response->message);
        $this->assertNull($response->data);
    }
}
