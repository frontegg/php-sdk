<?php

namespace Frontegg\Event\Type;

use ArrayObject;

interface ChannelsConfigInterface extends SerializableInterface
{
    /**
     * @return WebHookBody|null
     */
    public function getWebHook(): ?WebHookBody;

    /**
     * @return ArrayObject|null
     */
    public function getSlack(): ?ArrayObject;

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