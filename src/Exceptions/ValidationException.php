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
     * @param \Throwable|null $previous
     */
    public function __construct(
        string $message = "Validation failed",
        ?int $code = null,
        ?array $data = null,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $data, $previous);
    }

    /**
     * Get the validation errors.
     *
     * @return array|null
     */
    public function getErrors(): ?array
    {
        return $this->getErrorData();
    }
}