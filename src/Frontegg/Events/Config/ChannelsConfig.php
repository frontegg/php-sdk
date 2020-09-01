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
     * Body properties.
     * Pass instance of UseChannelDefaults class to send default properties.
     * If set to null then this channel data will not be sent.
     *
     * @var WebHookBody|UseChannelDefaults|null
     */
    protected $webHook;

    /**
     * Properties for this channel.
     * Pass instance of UseChannelDefaults class to send default properties.
     * If set to null then this channel data will not be sent.
     *
     * @var SlackChatPostMessageArgumentsInterface|UseChannelDefaults|null
     */
    protected $slack;

    /**
     * Properties for this channel.
     * Pass instance of UseChannelDefaults class to send default properties.
     * If set to null then this channel data will not be sent.
     *
     * @var WebPushPropertiesInterface|UseChannelDefaults|null
     */
    protected $webPush;

    /**
     * Properties for this channel.
     * Pass instance of UseChannelDefaults class to send default properties.
     * If set to null then this channel data will not be sent.
     *
     * @var AuditPropertiesInterface|UseChannelDefaults|null
     */
    protected $audit;

    /**
     * Properties for this channel.
     * Pass instance of UseChannelDefaults class to send default properties.
     * If set to null then this channel data will not be sent.
     *
     * @var BellPropertiesInterface|UseChannelDefaults|null
     */
    protected $bell;

    /**
     * ChannelsConfig constructor.
     *
     * @TODO: Refactor to use channel types (WebHookBody) as parameter types.
     *
     * @param WebHookBody|UseChannelDefaults|null                            $webHook
     * @param WebPushPropertiesInterface|UseChannelDefaults|null             $webPush
     * @param AuditPropertiesInterface|UseChannelDefaults|null               $audit
     * @param BellPropertiesInterface|UseChannelDefaults|null                $bell
     * @param SlackChatPostMessageArgumentsInterface|UseChannelDefaults|null $slack
     */
    public function __construct(
        ?UseChannelDefaults $webHook = null,
        ?UseChannelDefaults $webPush = null,
        ?UseChannelDefaults $audit = null,
        ?UseChannelDefaults $bell = null,
        ?UseChannelDefaults $slack = null
    ) {
        $this->webHook = $webHook;
        $this->slack = $slack;
        $this->webPush = $webPush;
        $this->audit = $audit;
        $this->bell = $bell;
    }

    /**
     * @return WebHookBody|UseChannelDefaults|null
     */
    public function getWebHook(): ?UseChannelDefaults
    {
        return $this->webHook;
    }

    /**
     * @param WebHookBody|UseChannelDefaults|null $webHook
     */
    public function setWebHook(?UseChannelDefaults $webHook): void
    {
        $this->webHook = $webHook;
    }

    /**
     * @return SlackChatPostMessageArgumentsInterface|UseChannelDefaults|null
     */
    public function getSlack(): ?UseChannelDefaults
    {
        return $this->slack;
    }

    /**
     * @param SlackChatPostMessageArgumentsInterface|UseChannelDefaults|null $slack
     */
    public function setSlack(?UseChannelDefaults $slack): void
    {
        $this->slack = $slack;
    }

    /**
     * @return WebPushPropertiesInterface|UseChannelDefaults|null
     */
    public function getWebPush(): ?UseChannelDefaults
    {
        return $this->webPush;
    }

    /**
     * @param WebPushPropertiesInterface|UseChannelDefaults|null $webPush
     */
    public function setWebPush(?UseChannelDefaults $webPush): void
    {
        $this->webPush = $webPush;
    }

    /**
     * @return AuditPropertiesInterface|UseChannelDefaults|null
     */
    public function getAudit(): ?UseChannelDefaults
    {
        return $this->audit;
    }

    /**
     * @param AuditPropertiesInterface|UseChannelDefaults|null $audit
     */
    public function setAudit(?UseChannelDefaults $audit): void
    {
        $this->audit = $audit;
    }

    /**
     * @return BellPropertiesInterface|UseChannelDefaults|null
     */
    public function getBell(): ?UseChannelDefaults
    {
        return $this->bell;
    }

    /**
     * @param BellPropertiesInterface|UseChannelDefaults|null $bell
     */
    public function setBell(?UseChannelDefaults $bell): void
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
        $channels = [];

        if ($this->webHook !== null) {
            $data = $this->webHook->toArray();
            $channels['webhook'] = $data !== [] ? $data : true;
        }
        if ($this->webPush !== null) {
            $data = $this->webPush->toArray();
            $channels['webpush'] = $data !== [] ? $data : true;
        }
        if ($this->audit !== null) {
            $data = $this->audit->toArray();
            $channels['audit'] = $data !== [] ? $data : true;
        }
        if ($this->bell !== null) {
            $data = $this->bell->toArray();
            $channels['bell'] = $data !== [] ? $data : true;
        }
        if ($this->slack !== null) {
            $data = $this->slack->toArray();
            $channels['slack'] = $data !== [] ? $data : true;
        }

        return $channels;
    }
}
