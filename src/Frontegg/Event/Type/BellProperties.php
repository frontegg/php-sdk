<?php

namespace Frontegg\Event\Type;

use DateTime;
use Frontegg\Exception\InvalidParameterException;

class BellProperties implements BellPropertiesInterface
{
    protected const SEVERITY_DEFAULT_VALUE = self::SEVERITY_INFO;

    /**
     * @TODO: Move to separate class NotificationSeverity.
     *
     * Audit severity possible values.
     *
     * @var string[]
     */
    protected static $SEVERITY_ALLOWED_VALUES = [
        self::SEVERITY_INFO,
        self::SEVERITY_MEDIUM,
        self::SEVERITY_HIGH,
        self::SEVERITY_CRITICAL,
    ];

    /**
     * Send the bell notification to specific user by his ID.
     *
     * @var string|null
     */
    protected $userId;

    /**
     * Notification title.
     *
     * @var string|null
     */
    protected $title;

    /**
     * Notification body.
     *
     * @var string|null
     */
    protected $body;

    /**
     * Notification severity. Default value is 'Info'.
     *
     * @var string
     */
    protected $severity;

    /**
     * Notification expiration date. By default the notification won't have
     * an expiration date.
     *
     * @var DateTime|null
     */
    protected $expiryDate;

    /**
     * The url that will be opened on a new window on click.
     *
     * @var string|null
     */
    protected $url;

    /**
     * Actions list to show in the notification.
     *
     * @var BellActionInterface[]
     */
    protected $actions;

    /**
     * BellProperties constructor.
     *
     * @param string|null           $userId
     * @param string|null           $title
     * @param string|null           $body
     * @param string|null           $severity
     * @param DateTime|null         $expiryDate
     * @param string|null           $url
     * @param BellActionInterface[] $actions
     */
    public function __construct(
        ?string $userId = null,
        ?string $title = null,
        ?string $body = null,
        ?string $severity = self::SEVERITY_DEFAULT_VALUE,
        ?DateTime $expiryDate = null,
        ?string $url = null,
        array $actions = []
    ) {
        $this->userId = $userId;
        $this->title = $title;
        $this->body = $body;
        $this->setSeverity($severity);
        $this->expiryDate = $expiryDate;
        $this->url = $url;
        $this->setActions($actions);
    }

    /**
     * @return string|null
     */
    public function getUserId(): ?string
    {
        return $this->userId;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @return string|null
     */
    public function getSeverity(): ?string
    {
        return $this->severity;
    }

    /**
     * @return DateTime|null
     */
    public function getExpiryDate(): ?DateTime
    {
        return $this->expiryDate;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @return BellActionInterface[]
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * @param array $actions
     *
     * @throws InvalidParameterException
     *
     * @return void
     */
    public function setActions(array $actions): void
    {
        foreach ($actions as $action) {
            if (!$action instanceof BellActionInterface) {
                throw new InvalidParameterException(
                    sprintf(
                        'Action class must be an instance of "%s" class. Given "%s"',
                        BellActionInterface::class,
                        get_class($action)
                    )
                );
            }
        }

        $this->actions = $actions;
    }

    /**
     * @param string|null $severity
     */
    public function setSeverity(?string $severity): void
    {
        if (!in_array($severity, static::$SEVERITY_ALLOWED_VALUES)) {
            $severity = self::SEVERITY_DEFAULT_VALUE;
        }

        $this->severity = $severity;
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
        $data = [
            'userId' => $this->userId,
            'title' => $this->title,
            'body' => $this->body,
            'severity' => $this->severity,
            'expiryDate' => $this->expiryDate ? $this->expiryDate->format('Y-m-d H:i:s') : null,
            'url' => $this->url,
            'actions' => [],
        ];

        foreach ($this->actions as $bellAction) {
            $data['actions'][] = $bellAction->toArray();
        }

        return $data;
    }
}