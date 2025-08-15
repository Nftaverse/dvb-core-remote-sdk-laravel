<?php

namespace DVB\Core\SDK\DTOs;

class IpfsFileDataDTO
{
    public ?string $cid;
    public ?string $url;
    public ?string $http_url;
    public ?int $size;
    public ?string $mime_type;
    public ?string $name;
    public ?string $cid_path;
    public ?string $cdn_url;

    public function __construct(
        ?string $cid,
        ?string $url,
        ?string $http_url,
        ?int $size,
        ?string $mime_type,
        ?string $name = null,
        ?string $cid_path = null,
        ?string $cdn_url = null
    ) {
        $this->cid = $cid;
        $this->url = $url;
        $this->http_url = $http_url;
        $this->size = $size;
        $this->mime_type = $mime_type;
        $this->name = $name;
        $this->cid_path = $cid_path;
        $this->cdn_url = $cdn_url;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['cid'] ?? null,
            $data['url'] ?? null,
            $data['http_url'] ?? null,
            isset($data['size']) ? (int)$data['size'] : null,
            $data['mime_type'] ?? null,
            $data['name'] ?? null,
            $data['cid_path'] ?? null,
            $data['cdn_url'] ?? null,
        );
    }
}