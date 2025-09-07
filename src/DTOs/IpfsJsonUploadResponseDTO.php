<?php

namespace DVB\Core\SDK\DTOs;

class IpfsJsonUploadResponseDTO extends ApiResponse
{
    /** @var IpfsFileDTO|null */
    public mixed $data;

    public function __construct(int $code, string $message, ?IpfsFileDTO $data = null)
    {
        parent::__construct($code, $message);
        $this->data = $data;
    }

    public static function fromArray(array $data): self
    {
        // JSON upload response format - data is at root level
        $fileData = null;
        if (isset($data['cid']) && isset($data['url'])) {
            $fileData = $data;
        }
        
        $file = $fileData ? IpfsFileDTO::fromArray($fileData) : null;

        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            $file
        );
    }
}