<?php

namespace Frontegg\Events\Config;

use Frontegg\Events\Channel\AuditPropertiesInterface;
use Frontegg\Events\Channel\BellPropertiesInterface;
use Frontegg\Events\Channel\SlackChatPostMessageArgumentsInterface;
use Frontegg\Events\Channel\WebHookBody;
use Frontegg\Events\Channel\WebPushPropertiesInterface;

interface ChannelsConfigInterface extends SerializableInterface
{
    /**
     * @return WebHookBody|null
     */
    public function getWebHook(): ?WebHookBody;

    /**
     * @return SlackChatPostMessageArgumentsInterface|null
     */
    public function getSlack(): ?SlackChatPostMessageArgumentsInterface;

    /**
     * @return WebPushPropertiesInterface|null
     */
    public function getWebPush(): ?WebPushPropertiesInterface;

    /**
     * @return AuditPropertiesInterface|null
     */
    public function getAudit(): ?AuditPropertiesInterface;

    /**
     * @return BellPropertiesInterface|null
     */
    public function getBell(): ?BellPropertiesInterface;

    /**
     * Check if at least one channel is configured.
     *
     * @return bool
     */
    public function isConfigured(): bool;
}
