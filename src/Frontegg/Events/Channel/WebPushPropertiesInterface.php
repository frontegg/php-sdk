<?php

namespace Frontegg\Events\Channel;

use Frontegg\Events\Config\SerializableInterface;

interface WebPushPropertiesInterface extends SerializableInterface
{
    /**
     * @return string|null
     */
    public function getTitle(): ?string;

    /**
     * @return string|null
     */
    public function getBody(): ?string;

    /**
     * @return string|null
     */
    public function getUserId(): ?string;
}
