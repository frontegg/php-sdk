<?php

namespace Frontegg\Events\Channel;

interface WebPushPropertiesInterface
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
