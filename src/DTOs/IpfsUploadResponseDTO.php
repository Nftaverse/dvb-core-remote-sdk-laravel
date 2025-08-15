<?php

namespace DVB\Core\SDK\DTOs;

class IpfsUploadResponseDTO extends ApiResponse
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
        $file = isset($data['data']) ? IpfsFileDTO::fromArray($data['data']) : null;

        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            $file
        );
    }
}
