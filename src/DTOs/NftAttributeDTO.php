<?php

namespace DVB\Core\SDK\DTOs;

class NftAttributeDTO
{
    public string $traitType;
    public mixed $value;
    public ?string $displayType;

    public function __construct(string $traitType, mixed $value, ?string $displayType = null)
    {
        $this->traitType = $traitType;
        $this->value = $value;
        $this->displayType = $displayType;
    }

    public static function fromArray(array $data): self
    {
        // Helper function to handle null values and preserve types
        $getValueOrNull = function($key1, $key2 = null) use ($data) {
            if (array_key_exists($key1, $data)) {
                // If the value is null, return empty string for mandatory fields
                if ($data[$key1] === null && in_array($key1, ['trait_type', 'traitType', 'value'])) {
                    return '';
                }
                return $data[$key1];
            }
            if ($key2 !== null && array_key_exists($key2, $data)) {
                // If the value is null, return empty string for mandatory fields
                if ($data[$key2] === null && in_array($key2, ['trait_type', 'traitType', 'value'])) {
                    return '';
                }
                return $data[$key2];
            }
            // If both keys are not set, return empty string for mandatory fields
            if (in_array($key1, ['trait_type', 'traitType', 'value'])) {
                return '';
            }
            return null;
        };

        return new self(
            $getValueOrNull('trait_type', 'traitType'),
            $getValueOrNull('value'),
            $getValueOrNull('display_type', 'displayType'),
        );
    }
}