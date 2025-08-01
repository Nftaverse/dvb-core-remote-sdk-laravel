<?php

namespace DVB\Core\SDK\Exceptions;

class InsufficientCreditException extends DvbApiException
{
    /**
     * Create a new InsufficientCreditException instance.
     *
     * @param string $message
     * @param int|null $code
     * @param array|null $data
     * @param \Exception|null $previous
     */
    public function __construct(
        string $message = "Insufficient credit",
        ?int $code = null,
        ?array $data = null,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $data, $previous);
    }
}