<?php

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    protected $error;
    protected $details;

    /**
     * Create a new API exception.
     *
     * @param string $message Human-readable error message
     * @param string $error Machine-readable error identifier
     * @param int $code HTTP status code
     * @param array $details Additional error details (optional)
     * @param \Throwable|null $previous Previous exception (optional)
     */
    public function __construct(
        string $message = 'An error occurred.',
        string $error = 'api_error',
        int $code = 400,
        array $details = [],
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->error = $error;
        $this->details = $details;
    }

    /**
     * Get the error identifier.
     *
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * Get additional error details.
     *
     * @return array
     */
    public function getDetails(): array
    {
        return $this->details;
    }
}