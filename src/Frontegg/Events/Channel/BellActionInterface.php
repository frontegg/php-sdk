<?php

namespace Frontegg\Events\Channel;

use Frontegg\Events\Config\SerializableInterface;

interface BellActionInterface extends SerializableInterface
{
    public const VISUALIZATION_BUTTON = 'Button';
    public const VISUALIZATION_LINK = 'Link';

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getUrl(): string;

    /**
     * @return string
     */
    public function getMethod(): string;

    /**
     * @return string
     */
    public function getVisualization(): string;
}
