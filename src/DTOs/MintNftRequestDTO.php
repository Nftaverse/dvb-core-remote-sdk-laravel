<?php

namespace DVB\Core\SDK\DTOs;

use InvalidArgumentException;

class MintNftRequestDTO
{
    /** @var int */
    public int $chainId;
    
    /** @var string */
    public string $address;
    
    /** @var string */
    public string $toAddress;
    
    /** @var int */
    public int $amount;
    
    /** @var string|null */
    public ?string $reference;
    
    /** @var string|null */
    public ?string $metadata;

    /**
     * @param int $chainId
     * @param string $address
     * @param string $toAddress
     * @param int $amount
     * @param string|null $reference
     * @param string|null $metadata JSON format metadata string
     * @throws InvalidArgumentException
     */
    public function __construct(
        int $chainId,
        string $address,
        string $toAddress,
        int $amount,
        ?string $reference = null,
        ?string $metadata = null
    ) {
        $this->chainId = $chainId;
        $this->address = $address;
        $this->toAddress = $toAddress;
        $this->amount = $amount;
        $this->reference = $reference;
        
        // Validate metadata format if provided
        if ($metadata !== null) {
            $this->validateMetadata($metadata);
        }
        
        $this->metadata = $metadata;
    }

    /**
     * Validate metadata format according to API requirements
     * 
     * @param string $metadata
     * @throws InvalidArgumentException
     */
    private function validateMetadata(string $metadata): void
    {
        // Check if it's a valid JSON string
        $decoded = json_decode($metadata, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException('Metadata must be a valid JSON string. Error: ' . json_last_error_msg());
        }
        
        // Metadata must be an array
        if (!is_array($decoded)) {
            throw new InvalidArgumentException('Metadata must be a JSON array');
        }
        
        // Validate each metadata item in the array
        foreach ($decoded as $metajson) {
            if ('' === $metajson) {
                continue;
            }
            
            // Check required fields
            $this->checkNftMetadataFieldRequired($metajson);
            
            // Check attributes
            $this->checkNftMetadataAttributes($metajson);
        }
    }

    /**
     * Check required fields in NFT metadata
     * 
     * @param mixed $metajson
     * @throws InvalidArgumentException
     */
    private function checkNftMetadataFieldRequired(mixed $metajson): void
    {
        if (isset($metajson['name'], $metajson['image'])) {
            // Check field types
            if (!is_string($metajson['name']) || !is_string($metajson['image'])) {
                throw new InvalidArgumentException('Metadata "name" and "image" fields must be strings');
            }
        } else {
            throw new InvalidArgumentException('Metadata must contain both "name" and "image" fields');
        }
    }

    /**
     * Check attributes in NFT metadata
     * 
     * @param mixed $metajson
     * @throws InvalidArgumentException
     */
    private function checkNftMetadataAttributes(mixed $metajson): void
    {
        if (isset($metajson['attributes'])) {
            // Check if attributes is an array
            if (!is_array($metajson['attributes'])) {
                throw new InvalidArgumentException('Metadata "attributes" must be an array');
            }

            if (0 === count($metajson['attributes'])) {
                return;
            }

            foreach ($metajson['attributes'] as $attribute) {
                // Check required fields in attribute
                if (isset($attribute['trait_type'], $attribute['value'])) {
                    if (
                        !is_string($attribute['trait_type'])
                        || (!is_string($attribute['value']) && !is_numeric($attribute['value']))
                    ) {
                        throw new InvalidArgumentException('Attribute "trait_type" must be a string and "value" must be a string or numeric');
                    }
                } else {
                    throw new InvalidArgumentException('Attribute must contain both "trait_type" and "value" fields');
                }
            }
        }
    }

    public function toArray(): array
    {
        $data = [
            'chain_id' => $this->chainId,
            'address' => $this->address,
            'to_address' => $this->toAddress,
            'amount' => $this->amount,
        ];

        if ($this->reference !== null) {
            $data['reference'] = $this->reference;
        }

        if ($this->metadata !== null) {
            $data['metadata'] = $this->metadata;
        }

        return $data;
    }
}