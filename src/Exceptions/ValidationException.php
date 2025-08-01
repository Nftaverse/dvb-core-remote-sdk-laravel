<?php

namespace DVB\Core\SDK\Exceptions;

class ValidationException extends DvbApiException
{
    /**
     * Create a new ValidationException instance.
     *
     * @param string $message
     * @param int|null $code
     * @param array|null $data
     * @param \Exception|null $previous
     */
    public function __construct(
        string $message = "Validation failed",
        ?int $code = null,
        ?array $data = null,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $data, $previous);
    }
}