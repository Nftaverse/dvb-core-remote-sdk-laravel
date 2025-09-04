<?php

namespace DVB\Core\SDK\Tests\Unit\DTOs;

use DVB\Core\SDK\DTOs\CollectionDeployStatusResponseDTO;
use DVB\Core\SDK\DTOs\CollectionDTO;
use DVB\Core\SDK\Tests\TestCase;
use DVB\Core\SDK\Enums\CollectionDeployStatusEnum;

class CollectionDeployStatusResponseDTOTest extends TestCase
{
    public function test_it_can_be_created_from_array_with_deploying_status(): void
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'status' => 'DEPLOYING',
                'collection' => null
            ]
        ];

        $dto = CollectionDeployStatusResponseDTO::fromArray($data);

        $this->assertInstanceOf(CollectionDeployStatusResponseDTO::class, $dto);
        $this->assertEquals(200, $dto->code);
        $this->assertEquals('Success', $dto->message);
        $this->assertEquals(CollectionDeployStatusEnum::DEPLOYING, $dto->status);
        $this->assertNull($dto->collection);
    }

    public function test_it_can_be_created_from_array_with_deploy_failed_status(): void
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'status' => 'DEPLOY_FAILED',
                'collection' => null
            ]
        ];

        $dto = CollectionDeployStatusResponseDTO::fromArray($data);

        $this->assertInstanceOf(CollectionDeployStatusResponseDTO::class, $dto);
        $this->assertEquals(200, $dto->code);
        $this->assertEquals('Success', $dto->message);
        $this->assertEquals(CollectionDeployStatusEnum::DEPLOY_FAILED, $dto->status);
        $this->assertNull($dto->collection);
    }

    public function test_it_can_be_created_from_array_with_listing_status_and_collection(): void
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'status' => 'LISTING',
                'collection' => [
                    'contract_address' => '0x123',
                    'name' => 'Test Collection',
                    'symbol' => 'TEST'
                ]
            ]
        ];

        $dto = CollectionDeployStatusResponseDTO::fromArray($data);

        $this->assertInstanceOf(CollectionDeployStatusResponseDTO::class, $dto);
        $this->assertEquals(200, $dto->code);
        $this->assertEquals('Success', $dto->message);
        $this->assertEquals(CollectionDeployStatusEnum::LISTING, $dto->status);
        $this->assertInstanceOf(CollectionDTO::class, $dto->collection);
        $this->assertEquals('Test Collection', $dto->collection->name);
    }

    public function test_it_can_be_created_from_array_with_pending_status(): void
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'status' => 'PENDING',
                'collection' => null
            ]
        ];

        $dto = CollectionDeployStatusResponseDTO::fromArray($data);

        $this->assertInstanceOf(CollectionDeployStatusResponseDTO::class, $dto);
        $this->assertEquals(200, $dto->code);
        $this->assertEquals('Success', $dto->message);
        $this->assertEquals(CollectionDeployStatusEnum::PENDING, $dto->status);
        $this->assertNull($dto->collection);
    }

    public function test_it_throws_exception_when_invalid_status_provided(): void
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'status' => 'INVALID_STATUS',
                'collection' => null
            ]
        ];

        $this->expectException(\ValueError::class);

        CollectionDeployStatusResponseDTO::fromArray($data);
    }

    public function test_it_throws_exception_when_no_status_provided(): void
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                // no status field
                'collection' => null
            ]
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Status field is required in collection deploy status response');

        CollectionDeployStatusResponseDTO::fromArray($data);
    }

    public function test_is_deployed_returns_true_when_status_is_listing_and_collection_exists(): void
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'status' => 'LISTING',
                'collection' => [
                    'contract_address' => '0x123',
                    'name' => 'Test Collection',
                    'symbol' => 'TEST'
                ]
            ]
        ];

        $dto = CollectionDeployStatusResponseDTO::fromArray($data);

        $this->assertTrue($dto->isDeployed());
    }

    public function test_is_deployed_returns_false_when_status_is_listing_but_no_collection(): void
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'status' => 'LISTING',
                'collection' => null
            ]
        ];

        $dto = CollectionDeployStatusResponseDTO::fromArray($data);

        $this->assertFalse($dto->isDeployed());
    }

    public function test_is_deployed_returns_false_when_status_is_not_listing(): void
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'status' => 'DEPLOYING',
                'collection' => [
                    'contract_address' => '0x123',
                    'name' => 'Test Collection',
                    'symbol' => 'TEST'
                ]
            ]
        ];

        $dto = CollectionDeployStatusResponseDTO::fromArray($data);

        $this->assertFalse($dto->isDeployed());
    }

    public function test_is_deploying_returns_true_when_status_is_deploying(): void
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'status' => 'DEPLOYING',
                'collection' => null
            ]
        ];

        $dto = CollectionDeployStatusResponseDTO::fromArray($data);

        $this->assertTrue($dto->isDeploying());
    }

    public function test_is_deploy_failed_returns_true_when_status_is_deploy_failed(): void
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'status' => 'DEPLOY_FAILED',
                'collection' => null
            ]
        ];

        $dto = CollectionDeployStatusResponseDTO::fromArray($data);

        $this->assertTrue($dto->isDeployFailed());
    }

    public function test_is_pending_returns_true_when_status_is_pending(): void
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'status' => 'PENDING',
                'collection' => null
            ]
        ];

        $dto = CollectionDeployStatusResponseDTO::fromArray($data);

        $this->assertTrue($dto->isPending());
    }

    public function test_it_throws_exception_when_data_is_not_array(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Data must be an array for CollectionDeployStatusResponseDTO');

        new CollectionDeployStatusResponseDTO(200, 'Success', 'not an array');
    }
}