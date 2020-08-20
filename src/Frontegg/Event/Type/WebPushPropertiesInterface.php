<?php

namespace Frontegg\Event\Type;

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