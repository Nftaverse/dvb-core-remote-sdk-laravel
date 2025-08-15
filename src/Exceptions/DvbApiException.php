<?php

namespace DVB\Core\SDK\Exceptions;

use Exception;

class DvbApiException extends Exception
{
    protected ?int $errorCode;
    protected ?array $errorData;
    protected ?\Throwable $previousException;

    /**
     * Create a new DvbApiException instance.
     *
     * @param string $message
     * @param int|null $code
     * @param array|null $data
     * @param \Throwable|null $previous
     */
    public function __construct(
        string $message = "",
        ?int $code = null,
        ?array $data = null,
        ?\Throwable $previous = null
    ) {
        $this->errorCode = $code;
        $this->errorData = $data;
        $this->previousException = $previous;
        
        parent::__construct($message, $code ?? 0, $previous);
    }

    /**
     * Get the error code.
     *
     * @return int|null
     */
    public function getErrorCode(): ?int
    {
        return $this->errorCode;
    }

    /**
     * Get the error data.
     *
     * @return array|null
     */
    public function getErrorData(): ?array
    {
        return $this->errorData;
    }

    /**
     * Get the previous exception.
     *
     * @return \Throwable|null
     */
    public function getPreviousException(): ?\Throwable
    {
        return $this->previousException;
    }
}