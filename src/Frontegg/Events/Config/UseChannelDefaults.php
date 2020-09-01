<?php

namespace Frontegg\Events\Config;

class UseChannelDefaults implements SerializableInterface
{
    /**
     * @inheritDoc
     */
    public function toJSON(): string
    {
        return 'true';
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [];
    }
}
