<?php

namespace DVB\Core\SDK\DTOs;

class CollectionDeployStatusResponseDTO extends ApiResponse
{
    public string $status;
    public ?CollectionDTO $collection;

    public function __construct(int $code, string $message, mixed $data = null)
    {
        parent::__construct($code, $message, $data);
        
        if (is_array($data)) {
            $this->status = $data['status'] ?? '';
            $this->collection = isset($data['collection']) ? CollectionDTO::fromArray($data['collection']) : null;
        } else {
            $this->status = '';
            $this->collection = null;
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
        return $this->status === 'listing' && $this->collection !== null;
    }
}