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
        // Folder upload response format - data is at root level with different field names
        $folderData = null;
        if (isset($data['folder_cid']) && isset($data['folder_url'])) {
            // Map folder_* fields to expected fields
            $folderData = [
                'cid' => $data['folder_cid'],
                'url' => $data['folder_url'],
            ];
        }
        
        $folder = $folderData ? IpfsFolderDTO::fromArray($folderData) : null;

        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            $folder
        );
    }
}
