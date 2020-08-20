<?php

namespace Frontegg\Event\Type;

interface SerializableInterface
{
    /**
     * Serialize the current object to JSON.
     *
     * @return string
     */
    public function toJSON(): string;

    /**
     * Serialize the current object as array.
     *
     * @return array
     */
    public function toArray(): array;
}
