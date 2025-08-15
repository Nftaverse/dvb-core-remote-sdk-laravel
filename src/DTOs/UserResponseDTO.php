<?php

namespace DVB\Core\SDK\DTOs;

class UserResponseDTO extends ApiResponse
{
    /** @var UserDTO|null */
    public mixed $data;

    public function __construct(int $code, string $message, ?UserDTO $data = null)
    {
        parent::__construct($code, $message);
        $this->data = $data;
    }

    public static function fromArray(array $data): self
    {
        $userDto = isset($data['data']) ? UserDTO::fromArray($data['data']) : null;
        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            $userDto
        );
    }
}
