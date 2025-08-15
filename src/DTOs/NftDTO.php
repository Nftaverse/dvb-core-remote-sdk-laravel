<?php

namespace DVB\Core\SDK\DTOs;

class NftDTO
{
    public string $tokenId;
    public string $contractAddress;
    public int $chainId;
    public string $name;
    public ?string $description;
    public ?string $image;
    /** @var NftAttributeDTO[]|null */
    public ?array $attributes;
    public ?string $externalUrl;
    public ?string $animationUrl;
    public ?string $backgroundColor;
    public ?string $youtubeUrl;

    /**
     * @param string $tokenId
     * @param string $contractAddress
     * @param int $chainId
     * @param string $name
     * @param string $description
     * @param string $image
     * @param NftAttributeDTO[]|null $attributes
     * @param string|null $externalUrl
     * @param string|null $animationUrl
     * @param string|null $backgroundColor
     * @param string|null $youtubeUrl
     */
    public function __construct(
        string $tokenId,
        string $contractAddress,
        int $chainId,
        string $name,
        ?string $description,
        ?string $image,
        ?array $attributes = null,
        ?string $externalUrl = null,
        ?string $animationUrl = null,
        ?string $backgroundColor = null,
        ?string $youtubeUrl = null
    ) {
        $this->tokenId = $tokenId;
        $this->contractAddress = $contractAddress;
        $this->chainId = $chainId;
        $this->name = $name;
        $this->description = $description;
        $this->image = $image;
        $this->attributes = $attributes;
        $this->externalUrl = $externalUrl;
        $this->animationUrl = $animationUrl;
        $this->backgroundColor = $backgroundColor;
        $this->youtubeUrl = $youtubeUrl;
    }

    public static function fromArray(array $data): self
    {
        $attributes = null;
        if (isset($data['attributes']) && is_array($data['attributes'])) {
            $attributes = [];
            foreach ($data['attributes'] as $attributeData) {
                $attributes[] = NftAttributeDTO::fromArray($attributeData);
            }
        }

        // Helper function to handle null values
        $getValueOrNull = function($key1, $key2 = null) use ($data) {
            if (array_key_exists($key1, $data)) {
                // If the value is null, return default values for mandatory fields
                if ($data[$key1] === null) {
                    if (in_array($key1, ['token_id', 'tokenId', 'contract_address', 'contractAddress', 'name'])) {
                        return '';
                    }
                    if (in_array($key1, ['chain_id', 'chainId'])) {
                        return 0;
                    }
                    return null; // For optional fields
                }
                return $data[$key1];
            }
            if ($key2 !== null && array_key_exists($key2, $data)) {
                // If the value is null, return default values for mandatory fields
                if ($data[$key2] === null) {
                    if (in_array($key2, ['token_id', 'tokenId', 'contract_address', 'contractAddress', 'name'])) {
                        return '';
                    }
                    if (in_array($key2, ['chain_id', 'chainId'])) {
                        return 0;
                    }
                    return null; // For optional fields
                }
                return $data[$key2];
            }
            // If both keys are not set, return default values for mandatory fields
            if (in_array($key1, ['token_id', 'tokenId', 'contract_address', 'contractAddress', 'name'])) {
                return '';
            }
            if (in_array($key1, ['chain_id', 'chainId'])) {
                return 0;
            }
            return null;
        };

        return new self(
            $getValueOrNull('token_id', 'tokenId'),
            $getValueOrNull('contract_address', 'contractAddress'),
            $getValueOrNull('chain_id', 'chainId'),
            $getValueOrNull('name'),
            $getValueOrNull('description'),
            $getValueOrNull('image'),
            $attributes,
            $getValueOrNull('external_url', 'externalUrl'),
            $getValueOrNull('animation_url', 'animationUrl'),
            $getValueOrNull('background_color', 'backgroundColor'),
            $getValueOrNull('youtube_url', 'youtubeUrl'),
        );
    }
}