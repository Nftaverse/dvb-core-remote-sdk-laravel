<?php

namespace DVB\Core\SDK\DTOs;

class NftMetadataDTO
{
    /**
     * @param string $name
     * @param string $description
     * @param string $image
     * @param NftAttributeDTO[]|null $attributes
     * @param string|null $externalUrl
     * @param string|null $animationUrl
     * @param string|null $backgroundColor
     * @param string|null $youtubeUrl
     * @param int|null $expiresAt
     * @param array|null $additionalData
     */
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly string $image,
        public readonly ?array $attributes = null,
        public readonly ?string $externalUrl = null,
        public readonly ?string $animationUrl = null,
        public readonly ?string $backgroundColor = null,
        public readonly ?string $youtubeUrl = null,
        public readonly ?int $expiresAt = null,
        public readonly ?array $additionalData = null,
    ) {
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

        // 提取額外數據，排除已有的字段
        $reservedKeys = [
            'name', 'description', 'image', 'attributes', 
            'external_url', 'externalUrl', 
            'animation_url', 'animationUrl', 
            'background_color', 'backgroundColor', 
            'youtube_url', 'youtubeUrl',
            'expires_at', 'expiresAt'
        ];
        
        $additionalData = [];
        foreach ($data as $key => $value) {
            if (!in_array($key, $reservedKeys)) {
                $additionalData[$key] = $value;
            }
        }

        return new self(
            $data['name'] ?? '',
            $data['description'] ?? '',
            $data['image'] ?? '',
            $attributes,
            $data['external_url'] ?? $data['externalUrl'] ?? null,
            $data['animation_url'] ?? $data['animationUrl'] ?? null,
            $data['background_color'] ?? $data['backgroundColor'] ?? null,
            $data['youtube_url'] ?? $data['youtubeUrl'] ?? null,
            $data['expires_at'] ?? $data['expiresAt'] ?? null,
            $additionalData ?: null,
        );
    }
}