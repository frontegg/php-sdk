<?php

namespace Frontegg\Events\Type;

use DateTime;

interface AuditPropertiesInterface extends SerializableInterface
{
    /**
     * @TODO: Move to separate class NotificationSeverity.
     */
    public const SEVERITY_INFO = 'Info';
    public const SEVERITY_MEDIUM = 'Medium';
    public const SEVERITY_HIGH = 'High';
    public const SEVERITY_CRITICAL = 'Critical';

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime;

    /**
     * @return string
     */
    public function getSeverity(): string;

    /**
     * @return array
     */
    public function getFields(): array;
}
