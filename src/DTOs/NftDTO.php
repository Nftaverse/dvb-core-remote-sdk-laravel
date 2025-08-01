<?php

namespace DVB\Core\SDK\DTOs;

class NftDTO
{
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
        public readonly string $tokenId,
        public readonly string $contractAddress,
        public readonly int $chainId,
        public readonly string $name,
        public readonly string $description,
        public readonly string $image,
        public readonly ?array $attributes = null,
        public readonly ?string $externalUrl = null,
        public readonly ?string $animationUrl = null,
        public readonly ?string $backgroundColor = null,
        public readonly ?string $youtubeUrl = null,
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

        return new self(
            $data['token_id'] ?? $data['tokenId'] ?? '',
            $data['contract_address'] ?? $data['contractAddress'] ?? '',
            $data['chain_id'] ?? $data['chainId'] ?? 0,
            $data['name'] ?? '',
            $data['description'] ?? '',
            $data['image'] ?? '',
            $attributes,
            $data['external_url'] ?? $data['externalUrl'] ?? null,
            $data['animation_url'] ?? $data['animationUrl'] ?? null,
            $data['background_color'] ?? $data['backgroundColor'] ?? null,
            $data['youtube_url'] ?? $data['youtubeUrl'] ?? null,
        );
    }
}