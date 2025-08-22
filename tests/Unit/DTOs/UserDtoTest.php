<?php

namespace DVB\Core\SDK\Tests\Unit\DTOs;

use DVB\Core\SDK\DTOs\UserDTO;
use DVB\Core\SDK\DTOs\UserResponseDTO;
use DVB\Core\SDK\Tests\TestCase;
use DVB\Core\SDK\DTOs\WalletDTO;

class UserDtoTest extends TestCase
{
    public function test_user_dto_can_be_created_from_array()
    {
        $data = [
            'uid' => 'user123',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'avatar' => 'https://example.com/avatar.jpg',
            'wallet' => [
                ['address' => '0x123', 'chain_id' => 1],
            ],
        ];

        $user = UserDTO::fromArray($data);

        $this->assertInstanceOf(UserDTO::class, $user);
        $this->assertEquals('user123', $user->uid);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals('https://example.com/avatar.jpg', $user->avatar);
        $this->assertIsArray($user->wallet);
        $this->assertInstanceOf(WalletDTO::class, $user->wallet[0]);
    }

    public function test_user_dto_can_be_created_with_missing_optional_fields()
    {
        $data = [
            'uid' => 'user123',
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ];

        $user = UserDTO::fromArray($data);

        $this->assertInstanceOf(UserDTO::class, $user);
        $this->assertEquals('user123', $user->uid);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertNull($user->avatar);
        $this->assertNull($user->wallet);
    }

    public function test_user_dto_can_be_created_with_null_values()
    {
        $data = [
            'uid' => 'user123',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'avatar' => null,
            'wallet' => null,
        ];

        $user = UserDTO::fromArray($data);

        $this->assertInstanceOf(UserDTO::class, $user);
        $this->assertEquals('user123', $user->uid);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertNull($user->avatar);
        $this->assertNull($user->wallet);
    }

    public function test_user_dto_handles_empty_strings()
    {
        $data = [
            'uid' => '',
            'name' => '',
            'email' => '',
        ];

        $user = UserDTO::fromArray($data);

        $this->assertInstanceOf(UserDTO::class, $user);
        $this->assertEquals('', $user->uid);
        $this->assertEquals('', $user->name);
        $this->assertEquals('', $user->email);
    }

    public function test_user_response_dto_can_be_created_from_array()
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'uid' => 'user123',
                'name' => 'John Doe',
                'email' => 'john@example.com',
            ],
        ];

        $response = UserResponseDTO::fromArray($data);

        $this->assertInstanceOf(UserResponseDTO::class, $response);
        $this->assertEquals(200, $response->code);
        $this->assertEquals('Success', $response->message);
        $this->assertInstanceOf(UserDTO::class, $response->data);
        $this->assertEquals('user123', $response->data->uid);
    }

    public function test_user_response_dto_can_handle_null_data()
    {
        $data = [
            'code' => 404,
            'message' => 'User not found',
            'data' => null,
        ];

        $response = UserResponseDTO::fromArray($data);

        $this->assertInstanceOf(UserResponseDTO::class, $response);
        $this->assertEquals(404, $response->code);
        $this->assertEquals('User not found', $response->message);
        $this->assertNull($response->data);
    }

    public function test_user_response_dto_handles_empty_data()
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'data' => [],
        ];

        $response = UserResponseDTO::fromArray($data);

        $this->assertInstanceOf(UserResponseDTO::class, $response);
        $this->assertEquals(200, $response->code);
        $this->assertEquals('Success', $response->message);
        $this->assertNotNull($response->data);
        $this->assertInstanceOf(UserDTO::class, $response->data);
    }

    public function test_user_response_dto_with_missing_fields()
    {
        $data = [
            'code' => 500,
            'message' => 'Internal Server Error',
        ];

        $response = UserResponseDTO::fromArray($data);

        $this->assertInstanceOf(UserResponseDTO::class, $response);
        $this->assertEquals(500, $response->code);
        $this->assertEquals('Internal Server Error', $response->message);
        $this->assertNull($response->data);
    }
}
