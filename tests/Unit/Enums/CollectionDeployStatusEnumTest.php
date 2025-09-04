<?php

namespace DVB\Core\SDK\Tests\Unit\Enums;

use DVB\Core\SDK\Enums\CollectionDeployStatusEnum;
use DVB\Core\SDK\Tests\TestCase;

class CollectionDeployStatusEnumTest extends TestCase
{
    public function test_enum_values(): void
    {
        $this->assertEquals('DEPLOYING', CollectionDeployStatusEnum::DEPLOYING->value);
        $this->assertEquals('DEPLOY_FAILED', CollectionDeployStatusEnum::DEPLOY_FAILED->value);
        $this->assertEquals('LISTING', CollectionDeployStatusEnum::LISTING->value);
        $this->assertEquals('PENDING', CollectionDeployStatusEnum::PENDING->value);
    }

    public function test_is_deploying_method(): void
    {
        $this->assertTrue(CollectionDeployStatusEnum::DEPLOYING->isDeploying());
        $this->assertFalse(CollectionDeployStatusEnum::DEPLOY_FAILED->isDeploying());
        $this->assertFalse(CollectionDeployStatusEnum::LISTING->isDeploying());
        $this->assertFalse(CollectionDeployStatusEnum::PENDING->isDeploying());
    }

    public function test_is_deploy_failed_method(): void
    {
        $this->assertFalse(CollectionDeployStatusEnum::DEPLOYING->isDeployFailed());
        $this->assertTrue(CollectionDeployStatusEnum::DEPLOY_FAILED->isDeployFailed());
        $this->assertFalse(CollectionDeployStatusEnum::LISTING->isDeployFailed());
        $this->assertFalse(CollectionDeployStatusEnum::PENDING->isDeployFailed());
    }

    public function test_is_deployed_method(): void
    {
        $this->assertFalse(CollectionDeployStatusEnum::DEPLOYING->isDeployed());
        $this->assertFalse(CollectionDeployStatusEnum::DEPLOY_FAILED->isDeployed());
        $this->assertTrue(CollectionDeployStatusEnum::LISTING->isDeployed());
        $this->assertFalse(CollectionDeployStatusEnum::PENDING->isDeployed());
    }

    public function test_is_pending_method(): void
    {
        $this->assertFalse(CollectionDeployStatusEnum::DEPLOYING->isPending());
        $this->assertFalse(CollectionDeployStatusEnum::DEPLOY_FAILED->isPending());
        $this->assertFalse(CollectionDeployStatusEnum::LISTING->isPending());
        $this->assertTrue(CollectionDeployStatusEnum::PENDING->isPending());
    }

    public function test_try_from_method_with_valid_values(): void
    {
        $this->assertEquals(CollectionDeployStatusEnum::DEPLOYING, CollectionDeployStatusEnum::tryFrom('DEPLOYING'));
        $this->assertEquals(CollectionDeployStatusEnum::DEPLOY_FAILED, CollectionDeployStatusEnum::tryFrom('DEPLOY_FAILED'));
        $this->assertEquals(CollectionDeployStatusEnum::LISTING, CollectionDeployStatusEnum::tryFrom('LISTING'));
        $this->assertEquals(CollectionDeployStatusEnum::PENDING, CollectionDeployStatusEnum::tryFrom('PENDING'));
    }

    public function test_try_from_method_with_invalid_value(): void
    {
        $this->assertNull(CollectionDeployStatusEnum::tryFrom('INVALID_STATUS'));
    }
}