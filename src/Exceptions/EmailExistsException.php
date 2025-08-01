<?php

namespace DVB\Core\SDK\Exceptions;

class EmailExistsException extends DvbApiException
{
    /**
     * Create a new EmailExistsException instance.
     *
     * @param string $message
     * @param int|null $code
     * @param array|null $data
     * @param \Exception|null $previous
     */
    public function __construct(
        string $message = "Email already exists",
        ?int $code = null,
        ?array $data = null,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $data, $previous);
    }
}