<?php

namespace Frontegg\Authenticator;

use DateTime;

class AccessToken
{
    /**
     * Access token value.
     *
     * @var string
     */
    protected $value;

    /**
     * Token expires date time.
     *
     * @var DateTime
     */
    protected $expiresAt;

    /**
     * AccessToken constructor.
     *
     * @param string   $value
     * @param DateTime $expiresAt
     */
    public function __construct(string $value, DateTime $expiresAt)
    {
        $this->value = $value;
        $this->expiresAt = $expiresAt;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return DateTime
     */
    public function getExpiresAt(): DateTime
    {
        return $this->expiresAt;
    }

    /**
     * Check whether current access token is valid or not.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        if (!$this->value
            || $this->expiresAt->getTimestamp() <= ((new DateTime())->getTimestamp())
        ) {
            return false;
        }

        return true;
    }
}