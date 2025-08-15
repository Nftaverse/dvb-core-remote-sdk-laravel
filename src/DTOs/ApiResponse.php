<?php

namespace DVB\Core\SDK\DTOs;

class ApiResponse
{
    public int $code;
    public string $message;
    public mixed $data = null;

    public function __construct(int $code, string $message, mixed $data = null)
    {
        $this->code = $code;
        $this->message = $message;
        $this->data = $data;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['code'] ?? 0,
            $data['message'] ?? '',
            $data['data'] ?? null,
        );
    }
}