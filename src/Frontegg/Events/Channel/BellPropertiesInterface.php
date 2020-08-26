<?php

namespace Frontegg\Events\Channel;

use DateTime;
use Frontegg\Events\Config\SerializableInterface;

interface BellPropertiesInterface extends SerializableInterface
{
    /**
     * @TODO: Move to separate class NotificationSeverity.
     */
    public const SEVERITY_INFO = 'Info';
    public const SEVERITY_MEDIUM = 'Medium';
    public const SEVERITY_HIGH = 'High';
    public const SEVERITY_CRITICAL = 'Critical';

    /**
     * @return string|null
     */
    public function getUserId(): ?string;

    /**
     * @return string|null
     */
    public function getTitle(): ?string;

    /**
     * @return string|null
     */
    public function getBody(): ?string;

    /**
     * @return string|null
     */
    public function getSeverity(): ?string;

    /**
     * @return DateTime|null
     */
    public function getExpiryDate(): ?DateTime;

    /**
     * @return string|null
     */
    public function getUrl(): ?string;

    /**
     * @return BellActionInterface[]
     */
    public function getActions(): array;
}
