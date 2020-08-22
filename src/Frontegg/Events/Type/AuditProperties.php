<?php

namespace Frontegg\Events\Type;

use DateTime;

class AuditProperties implements AuditPropertiesInterface
{
    protected const SEVERITY_DEFAULT_VALUE = self::SEVERITY_INFO;

    /**
     * @TODO: Move to separate class NotificationSeverity.
     *
     * Audit severity possible values.
     *
     * @var string[]
     */
    protected static $SEVERITY_ALLOWED_VALUES
        = [
            self::SEVERITY_INFO,
            self::SEVERITY_MEDIUM,
            self::SEVERITY_HIGH,
            self::SEVERITY_CRITICAL,
        ];

    /**
     * Audit creation time. Default is the time audit accepted (now).
     *
     * @var DateTime
     */
    protected $createdAt;

    /**
     * Audit severity. Default value is 'Info'.
     *
     * @var string
     */
    protected $severity;

    /**
     * Additional fields the audit contain.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * AuditProperties constructor.
     *
     * @param string|null   $severity
     * @param DateTime|null $createdAt
     * @param string[]      $fields
     */
    public function __construct(
        ?string $severity = self::SEVERITY_DEFAULT_VALUE,
        ?DateTime $createdAt = null,
        array $fields = []
    ) {
        $this->setSeverity($severity);
        $this->createdAt = $createdAt ?? new DateTime();
        $this->fields = $fields;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getSeverity(): string
    {
        return $this->severity;
    }

    /**
     * @param string $severity
     */
    public function setSeverity(string $severity): void
    {
        if (!in_array($severity, static::$SEVERITY_ALLOWED_VALUES)) {
            $severity = self::SEVERITY_DEFAULT_VALUE;
        }

        $this->severity = $severity;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     */
    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    /**
     * @inheritDoc
     */
    public function toJSON(): string
    {
        return json_encode($this->toArray(), JSON_FORCE_OBJECT);
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->fields as $key => $field) {
            $data[$key] = $field;
        }

        $data['severity'] = $this->severity;
        $data['createdAt'] = $this->createdAt->format('Y-m-d H:i:s');

        return $data;
    }
}
