<?php

namespace DVB\Core\SDK\DTOs;

class IpfsFolderDTO
{
    public string $cid;
    public string $url;

    public function __construct(string $cid, string $url)
    {
        $this->cid = $cid;
        $this->url = $url;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['cid'] ?? '',
            $data['url'] ?? '',
        );
    }
}
