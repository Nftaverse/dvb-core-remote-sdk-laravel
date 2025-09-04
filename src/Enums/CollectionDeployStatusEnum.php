<?php

namespace DVB\Core\SDK\Enums;

enum CollectionDeployStatusEnum: string
{
    // Launchpad deployment statuses
    case DEPLOYING = 'DEPLOYING';
    case DEPLOY_FAILED = 'DEPLOY_FAILED';

    // Collection statuses (after successful deployment)
    case LISTING = 'LISTING';
    case PENDING = 'PENDING';
    
    /**
     * Check if the deployment is in progress.
     */
    public function isDeploying(): bool
    {
        return $this === self::DEPLOYING;
    }
    
    /**
     * Check if the deployment has failed.
     */
    public function isDeployFailed(): bool
    {
        return $this === self::DEPLOY_FAILED;
    }
    
    /**
     * Check if the collection is successfully deployed and available.
     */
    public function isDeployed(): bool
    {
        return $this === self::LISTING;
    }
    
    /**
     * Check if the collection is pending.
     */
    public function isPending(): bool
    {
        return $this === self::PENDING;
    }
}