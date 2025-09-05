<?php

namespace DVB\Core\SDK\Tests\Feature;

use DVB\Core\SDK\DvbApiClient;
use DVB\Core\SDK\DTOs\UserResponseDTO;
use DVB\Core\SDK\DTOs\UserDTO;
use DVB\Core\SDK\DTOs\WalletDTO;
use DVB\Core\SDK\DTOs\UserNftResponseDTO;
use DVB\Core\SDK\DTOs\CheckPermissionResponseDTO;
use DVB\Core\SDK\Exceptions\DvbApiException;
use Illuminate\Support\Facades\Http;
use DVB\Core\SDK\Tests\TestCase;

class UserApiTest extends TestCase
{
    private DvbApiClient $client;


    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->app->make(DvbApiClient::class);
        $this->client->useLaravelHttp(); // Enable Laravel Http facade for Feature tests
        config()->set('services.dvb.api_key', env('DVB_API_KEY'));
        config()->set('services.dvb.domain', env('DVB_API_DOMAIN'));
    }


    /** @test */
    public function it_can_create_user_with_email_only(): void
    {
        Http::fake([
            'dev-epoch.nft-investment.io/api/remote/v1/user*' => Http::response([
                'code' => 200,
                'message' => 'User created',
                'data' => [
                    'uid' => '12345',
                    'email' => 'test@example.com',
                ]
            ], 200)
        ]);

        $response = $this->client->createUser('test@example.com');

        $this->assertInstanceOf(UserResponseDTO::class, $response);
        $this->assertEquals(200, $response->code);
        $this->assertInstanceOf(UserDTO::class, $response->data);
        $this->assertEquals('12345', $response->data->uid);
        $this->assertEquals('test@example.com', $response->data->email);
    }

    /** @test */
    public function it_cannot_create_user_without_email_phone_or_sso_uid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('At least one of email, phone, or ssoUid must be provided');

        $this->client->createUser();
    }

    /** @test */
    public function it_can_create_user_with_phone_only(): void
    {
        Http::fake([
            'dev-epoch.nft-investment.io/api/remote/v1/user*' => Http::response([
                'code' => 200,
                'message' => 'User created',
                'data' => [
                    'uid' => '12345',
                    'email' => 'test@example.com',
                ]
            ], 200)
        ]);

        $response = $this->client->createUser(null, null, '+1234567890');

        $this->assertInstanceOf(UserResponseDTO::class, $response);
        $this->assertEquals(200, $response->code);
        $this->assertInstanceOf(UserDTO::class, $response->data);
        $this->assertEquals('12345', $response->data->uid);
        $this->assertEquals('test@example.com', $response->data->email);
    }

    /** @test */
    public function it_can_create_user_with_email_and_name(): void
    {
        Http::fake([
            'dev-epoch.nft-investment.io/api/remote/v1/user*' => Http::response([
                'code' => 200,
                'message' => 'User created',
                'data' => [
                    'uid' => '12345',
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                ]
            ], 200)
        ]);

        $response = $this->client->createUser('test@example.com', 'Test User');

        $this->assertInstanceOf(UserResponseDTO::class, $response);
        $this->assertEquals(200, $response->code);
        $this->assertInstanceOf(UserDTO::class, $response->data);
        $this->assertEquals('12345', $response->data->uid);
        $this->assertEquals('Test User', $response->data->name);
    }

    /** @test */
    public function it_can_create_user_with_email_name_and_phone(): void
    {
        Http::fake([
            'dev-epoch.nft-investment.io/api/remote/v1/user*' => Http::response([
                'code' => 200,
                'message' => 'User created',
                'data' => [
                    'uid' => '12345',
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                ]
            ], 200)
        ]);

        $response = $this->client->createUser('test@example.com', 'Test User', '+1234567890');

        $this->assertInstanceOf(UserResponseDTO::class, $response);
        $this->assertEquals(200, $response->code);
        $this->assertInstanceOf(UserDTO::class, $response->data);
        $this->assertEquals('12345', $response->data->uid);
        $this->assertEquals('Test User', $response->data->name);
    }

    /** @test */
    public function it_can_create_user_with_all_parameters(): void
    {
        Http::fake([
            'dev-epoch.nft-investment.io/api/remote/v1/user*' => Http::response([
                'code' => 200,
                'message' => 'User created',
                'data' => [
                    'uid' => '12345',
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                ]
            ], 200)
        ]);

        $response = $this->client->createUser(
            'test@example.com', 
            'Test User', 
            '+1234567890', 
            'sso123', 
            'NFTIV', 
            'defaultPassword123'
        );

        $this->assertInstanceOf(UserResponseDTO::class, $response);
        $this->assertEquals(200, $response->code);
        $this->assertInstanceOf(UserDTO::class, $response->data);
        $this->assertEquals('12345', $response->data->uid);
        $this->assertEquals('Test User', $response->data->name);
    }

    /** @test */
    public function it_can_create_user_with_sso_uid_only(): void
    {
        Http::fake([
            'dev-epoch.nft-investment.io/api/remote/v1/user*' => Http::response([
                'code' => 200,
                'message' => 'User created',
                'data' => [
                    'uid' => '12345',
                    'email' => 'test@example.com',
                ]
            ], 200)
        ]);

        $response = $this->client->createUser(null, null, null, 'sso123');

        $this->assertInstanceOf(UserResponseDTO::class, $response);
        $this->assertEquals(200, $response->code);
        $this->assertInstanceOf(UserDTO::class, $response->data);
        $this->assertEquals('12345', $response->data->uid);
        $this->assertEquals('test@example.com', $response->data->email);
    }

    /** @test */
    public function it_can_create_user_with_type_parameter(): void
    {
        Http::fake([
            'dev-epoch.nft-investment.io/api/remote/v1/user*' => Http::response([
                'code' => 200,
                'message' => 'User created',
                'data' => [
                    'uid' => '12345',
                    'email' => 'test@example.com',
                ]
            ], 200)
        ]);

        $response = $this->client->createUser('test@example.com', null, null, null, 'CUSTONOMY');

        $this->assertInstanceOf(UserResponseDTO::class, $response);
        $this->assertEquals(200, $response->code);
        $this->assertInstanceOf(UserDTO::class, $response->data);
        $this->assertEquals('12345', $response->data->uid);
        $this->assertEquals('test@example.com', $response->data->email);
    }

    /** @test */
    public function it_can_create_user_with_default_password(): void
    {
        Http::fake([
            'dev-epoch.nft-investment.io/api/remote/v1/user*' => Http::response([
                'code' => 200,
                'message' => 'User created',
                'data' => [
                    'uid' => '12345',
                    'email' => 'test@example.com',
                ]
            ], 200)
        ]);

        $response = $this->client->createUser('test@example.com', null, null, null, null, 'securePassword123');

        $this->assertInstanceOf(UserResponseDTO::class, $response);
        $this->assertEquals(200, $response->code);
        $this->assertInstanceOf(UserDTO::class, $response->data);
        $this->assertEquals('12345', $response->data->uid);
        $this->assertEquals('test@example.com', $response->data->email);
    }

    /** @test */
    public function it_handles_user_creation_failure_when_email_exists(): void
    {
        Http::fake([
            'dev-epoch.nft-investment.io/api/remote/v1/user*' => Http::response([
                'code' => 1007,
                'message' => 'Email already exists (0)',
            ], 400)
        ]);

        $this->expectException(DvbApiException::class);

        $this->client->createUser('exists@example.com');
    }

    /** @test */
    public function it_can_get_user_by_id(): void
    {
        $fakeUserData = [
            'uid' => 'user-123',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'wallet' => [
                ['wallet_address' => '0x123', 'is_treasury' => true]
            ]
        ];

        Http::fake([
            'dev-epoch.nft-investment.io/api/remote/v1/user/user-123' => Http::response([
                'code' => 200,
                'message' => 'Get user success',
                'data' => $fakeUserData
            ], 200)
        ]);

        $response = $this->client->getUser('user-123');

        $this->assertInstanceOf(UserResponseDTO::class, $response);
        $this->assertEquals('user-123', $response->data->uid);
        $this->assertCount(1, $response->data->wallet);
        $this->assertInstanceOf(WalletDTO::class, $response->data->wallet[0]);
    }

    /** @test */
    public function it_can_get_nfts_by_user(): void
    {
        Http::fake([
            'dev-epoch.nft-investment.io/api/remote/v1/user/user-123/nft*' => Http::response([
                'code' => 200,
                'message' => 'Get user nft success',
                'data' => [
                    'cursor' => 'next-page-token',
                    'items' => [
                        ['token_id' => '1', 'name' => 'NFT 1'],
                    ]
                ]
            ], 200)
        ]);

        $response = $this->client->getNftsByUser('user-123', 1);

        $this->assertInstanceOf(UserNftResponseDTO::class, $response);
        $this->assertEquals('next-page-token', $response->getNextCursor());
        $this->assertCount(1, $response->getItems());
    }

    /** @test */
    public function it_can_check_permission_with_string(): void
    {
        Http::fake([
            'dev-epoch.nft-investment.io/api/remote/v1/permission/check' => Http::response([
                'code' => 200,
                'data' => [
                    'hasPermission' => true
                ]
            ], 200)
        ]);

        $response = $this->client->checkPermission('MINT_NFT');

        $this->assertInstanceOf(CheckPermissionResponseDTO::class, $response);
        $this->assertTrue($response->data);
    }

    /** @test */
    public function it_can_check_permission_with_array(): void
    {
        Http::fake([
            'dev-epoch.nft-investment.io/api/remote/v1/permission/check' => Http::response([
                'code' => 200,
                'data' => [
                    'hasPermission' => true
                ]
            ], 200)
        ]);

        $response = $this->client->checkPermission(['MINT_NFT', 'CREATE_USER']);

        $this->assertInstanceOf(CheckPermissionResponseDTO::class, $response);
        $this->assertTrue($response->data);
    }
}
