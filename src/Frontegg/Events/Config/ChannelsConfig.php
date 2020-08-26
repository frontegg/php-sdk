<?php

namespace Frontegg\Events\Config;

use Frontegg\Events\Channel\AuditPropertiesInterface;
use Frontegg\Events\Channel\BellPropertiesInterface;
use Frontegg\Events\Channel\SlackChatPostMessageArgumentsInterface;
use Frontegg\Events\Channel\WebHookBody;
use Frontegg\Events\Channel\WebPushPropertiesInterface;

class ChannelsConfig implements ChannelsConfigInterface
{
    /**
     * Body properties. If set to null then default properties will be sent in the body.
     *
     * @var WebHookBody|null
     */
    protected $webHook;

    /**
     * Properties for this channel. If set to null then default properties will be sent.
     *
     * @var SlackChatPostMessageArgumentsInterface|null
     */
    protected $slack;

    /**
     * Properties for this channel. If set to null then default properties will be sent.
     *
     * @var WebPushPropertiesInterface|null
     */
    protected $webPush;

    /**
     * Properties for this channel. If set to null then default properties will be sent.
     *
     * @var AuditPropertiesInterface|null
     */
    protected $audit;

    /**
     * Properties for this channel. If set to null then default properties will be sent.
     *
     * @var BellPropertiesInterface|null
     */
    protected $bell;

    /**
     * ChannelsConfig constructor.
     *
     * @param WebHookBody|null                            $webHook
     * @param WebPushPropertiesInterface|null             $webPush
     * @param AuditPropertiesInterface|null               $audit
     * @param BellPropertiesInterface|null                $bell
     * @param SlackChatPostMessageArgumentsInterface|null $slack
     */
    public function __construct(
        ?WebHookBody $webHook = null,
        ?WebPushPropertiesInterface $webPush = null,
        ?AuditPropertiesInterface $audit = null,
        ?BellPropertiesInterface $bell = null,
        ?SlackChatPostMessageArgumentsInterface $slack = null
    ) {
        $this->webHook = $webHook;
        $this->slack = $slack;
        $this->webPush = $webPush;
        $this->audit = $audit;
        $this->bell = $bell;
    }

    /**
     * @return WebHookBody|null
     */
    public function getWebHook(): ?WebHookBody
    {
        return $this->webHook;
    }

    /**
     * @param WebHookBody|null $webHook
     */
    public function setWebHook(?WebHookBody $webHook): void
    {
        $this->webHook = $webHook;
    }

    /**
     * @return SlackChatPostMessageArgumentsInterface|null
     */
    public function getSlack(): ?SlackChatPostMessageArgumentsInterface
    {
        return $this->slack;
    }

    /**
     * @param SlackChatPostMessageArgumentsInterface|null $slack
     */
    public function setSlack(?SlackChatPostMessageArgumentsInterface $slack): void
    {
        $this->slack = $slack;
    }

    /**
     * @return WebPushPropertiesInterface|null
     */
    public function getWebPush(): ?WebPushPropertiesInterface
    {
        return $this->webPush;
    }

    /**
     * @param WebPushPropertiesInterface|null $webPush
     */
    public function setWebPush(?WebPushPropertiesInterface $webPush): void
    {
        $this->webPush = $webPush;
    }

    /**
     * @return AuditPropertiesInterface|null
     */
    public function getAudit(): ?AuditPropertiesInterface
    {
        return $this->audit;
    }

    /**
     * @param AuditPropertiesInterface|null $audit
     */
    public function setAudit(?AuditPropertiesInterface $audit): void
    {
        $this->audit = $audit;
    }

    /**
     * @return BellPropertiesInterface|null
     */
    public function getBell(): ?BellPropertiesInterface
    {
        return $this->bell;
    }

    /**
     * @param BellPropertiesInterface|null $bell
     */
    public function setBell(?BellPropertiesInterface $bell): void
    {
        $this->bell = $bell;
    }

    /**
     * Check if at least one channel is configured.
     *
     * @return bool
     */
    public function isConfigured(): bool
    {
        if (
            $this->webHook !== null
            || $this->webPush !== null
            || $this->audit !== null
            || $this->bell !== null
            || $this->slack !== null
        ) {
            return true;
        }

        return false;
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
        return [
            'webhook' => $this->webHook !== null ? $this->webHook->toArray()
                : true,
            'webpush' => $this->webPush !== null ? $this->webPush->toArray()
                : true,
            'audit' => $this->audit !== null ? $this->audit->toArray() : true,
            'bell' => $this->bell !== null ? $this->bell->toArray() : true,
            'slack' => $this->slack !== null ? $this->slack->toArray() : true,
        ];
    }
}
