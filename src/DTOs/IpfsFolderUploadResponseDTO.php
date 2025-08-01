<?php

namespace DVB\Core\SDK\DTOs;

class IpfsFolderUploadResponseDTO
{
    /**
     * @param int $code
     * @param string $message
     * @param IpfsFileDataDTO[]|null $data
     */
    public function __construct(
        public readonly int $code,
        public readonly string $message,
        public readonly ?array $data,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $files = null;
        if (isset($data['data']) && is_array($data['data'])) {
            $files = [];
            foreach ($data['data'] as $fileData) {
                $files[] = IpfsFileDataDTO::fromArray($fileData);
            }
        }

        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            $files,
        );
    }
}