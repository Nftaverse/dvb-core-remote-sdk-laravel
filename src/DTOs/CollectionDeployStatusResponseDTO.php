<?php

namespace DVB\Core\SDK\DTOs;

use DVB\Core\SDK\Enums\CollectionDeployStatusEnum;

class CollectionDeployStatusResponseDTO extends ApiResponse
{
    public CollectionDeployStatusEnum $status;
    public ?CollectionDTO $collection;

    public function __construct(int $code, string $message, mixed $data = null)
    {
        parent::__construct($code, $message, $data);
        
        if (is_array($data)) {
            // Convert string status to enum, throw exception if invalid
            $statusString = $data['status'] ?? null;
            if ($statusString === null) {
                throw new \InvalidArgumentException('Status field is required in collection deploy status response');
            }
            
            $this->status = CollectionDeployStatusEnum::from(strtoupper($statusString));
            $this->collection = isset($data['collection']) && is_array($data['collection']) ? CollectionDTO::fromArray($data['collection']) : null;
        } else {
            throw new \InvalidArgumentException('Data must be an array for CollectionDeployStatusResponseDTO');
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            $data['data'] ?? null,
        );
    }
    
    /**
     * Check if the collection is deployed and available.
     *
     * @return bool
     */
    public function isDeployed(): bool
    {
        return $this->status->isDeployed() && $this->collection !== null;
    }
    
    /**
     * Check if the collection deployment is in progress.
     *
     * @return bool
     */
    public function isDeploying(): bool
    {
        return $this->status->isDeploying();
    }
    
    /**
     * Check if the collection deployment has failed.
     *
     * @return bool
     */
    public function isDeployFailed(): bool
    {
        return $this->status->isDeployFailed();
    }
    
    /**
     * Check if the collection is pending.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status->isPending();
    }
    
    /**
     * Get the string value of the status.
     *
     * @return string
     */
    public function getStatusString(): string
    {
        return $this->status->value;
    }
}