<?php

namespace Frontegg\Authenticator;

/**
 * Class ApiError to handle API errors.
 *
 * @package Frontegg
 */
class ApiError
{
    /**
     * @var int|null
     */
    protected $statusCode;

    /**
     * @var string
     */
    protected $error;

    /**
     * @var string
     */
    protected $message;

    /**
     * ApiError constructor.
     *
     * @param string $error
     * @param string $message
     * @param int    $statusCode
     */
    public function __construct(
        string $error,
        string $message,
        int $statusCode = null
    ) {
        $this->statusCode = $statusCode;
        $this->error = $error;
        $this->message = $message;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
