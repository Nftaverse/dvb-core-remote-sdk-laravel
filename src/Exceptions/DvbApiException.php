<?php

namespace DVB\Core\SDK\Exceptions;

use Exception;

class DvbApiException extends Exception
{
    protected ?int $errorCode;
    protected ?array $errorData;
    protected ?Exception $previousException;

    /**
     * Create a new DvbApiException instance.
     *
     * @param string $message
     * @param int|null $code
     * @param array|null $data
     * @param \Exception|null $previous
     */
    public function __construct(
        string $message = "",
        ?int $code = null,
        ?array $data = null,
        ?Exception $previous = null
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
     * @return \Exception|null
     */
    public function getPreviousException(): ?Exception
    {
        return $this->previousException;
    }
}