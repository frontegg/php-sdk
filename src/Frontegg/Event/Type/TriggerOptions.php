<?php

namespace Frontegg\Event\Type;

class TriggerOptions implements TriggerOptionsInterface
{
    /**
     * Event key to trigger channel configuration by.
     *
     * @var string
     */
    protected $eventKey;

    /**
     * Default properties for all the channels. Can be overriden in the channel configuration.
     *
     * @var DefaultPropertiesInterface
     */
    protected $defaultProperties;

    /**
     * Trigger the event for a specific tenant.
     *
     * @var string|null
     */
    protected $tenantId;

    /**
     * Configuration of the channels the event will be sent to.
     *
     * @var ChannelsConfigInterface
     */
    protected $channels;

    /**
     * TriggerOptions constructor.
     *
     * @param string $eventKey
     * @param DefaultPropertiesInterface $defaultProperties
     * @param ChannelsConfigInterface $channels
     * @param string|null $tenantId
     */
    public function __construct(
        string $eventKey,
        DefaultPropertiesInterface $defaultProperties,
        ChannelsConfigInterface $channels,
        ?string $tenantId = null
    ) {
        $this->eventKey = $eventKey;
        $this->channels = $channels;
        $this->defaultProperties = $defaultProperties;
        $this->tenantId = $tenantId;
    }

    /**
     * @return string
     */
    public function getEventKey(): string
    {
        return $this->eventKey;
    }

    /**
     * @return DefaultPropertiesInterface
     */
    public function getDefaultProperties(): DefaultPropertiesInterface
    {
        return $this->defaultProperties;
    }

    /**
     * @return string|null
     */
    public function getTenantId(): ?string
    {
        return $this->tenantId;
    }

    /**
     * @return ChannelsConfigInterface
     */
    public function getChannels(): ChannelsConfigInterface
    {
        return $this->channels;
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
            'eventKey' => $this->eventKey,
            'properties' => $this->defaultProperties->toArray(),
            'tenantId' => $this->tenantId,
            'channels' => $this->channels->toArray(),
        ];
    }
}
