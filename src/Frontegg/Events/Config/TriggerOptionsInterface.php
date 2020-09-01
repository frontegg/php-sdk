<?php

namespace Frontegg\Events\Config;

interface TriggerOptionsInterface extends SerializableInterface
{
    /**
     * @return string
     */
    public function getEventKey(): string;

    /**
     * @return DefaultPropertiesInterface
     */
    public function getDefaultProperties(): DefaultPropertiesInterface;

    /**
     * @return string|null
     */
    public function getTenantId(): ?string;

    /**
     * @return ChannelsConfigInterface
     */
    public function getChannels(): ChannelsConfigInterface;
}
