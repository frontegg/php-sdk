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
     * @return WebHookBody|UseChannelDefaults|null
     */
    public function getWebHook(): ?UseChannelDefaults;

    /**
     * @return SlackChatPostMessageArgumentsInterface|UseChannelDefaults|null
     */
    public function getSlack(): ?UseChannelDefaults;

    /**
     * @return WebPushPropertiesInterface|UseChannelDefaults|null
     */
    public function getWebPush(): ?UseChannelDefaults;

    /**
     * @return AuditPropertiesInterface|UseChannelDefaults|null
     */
    public function getAudit(): ?UseChannelDefaults;

    /**
     * @return BellPropertiesInterface|UseChannelDefaults|null
     */
    public function getBell(): ?UseChannelDefaults;

    /**
     * Check if at least one channel is configured.
     *
     * @return bool
     */
    public function isConfigured(): bool;
}
