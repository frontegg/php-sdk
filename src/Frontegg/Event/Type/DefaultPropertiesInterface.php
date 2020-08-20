<?php

namespace Frontegg\Event\Type;

interface DefaultPropertiesInterface extends SerializableInterface
{
    /**
     * @return string|null
     */
    public function getTitle(): ?string;

    /**
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * @return array
     */
    public function getAdditionalProperties(): array;
}