<?php

namespace DVB\Core\SDK\DTOs;

class IpfsFolderUploadResponseDTO extends ApiResponse
{
    /** @var IpfsFolderDTO|null */
    public mixed $data;

    public function __construct(int $code, string $message, ?IpfsFolderDTO $data = null)
    {
        parent::__construct($code, $message);
        $this->data = $data;
    }

    public static function fromArray(array $data): self
    {
        $folder = isset($data['data']) ? IpfsFolderDTO::fromArray($data['data']) : null;

        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            $folder
        );
    }
}
