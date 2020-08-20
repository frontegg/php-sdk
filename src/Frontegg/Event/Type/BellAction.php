<?php

namespace Frontegg\Event\Type;

use Frontegg\Exception\InvalidParameterException;
use Frontegg\Http\RequestInterface;

class BellAction implements BellActionInterface
{
    /**
     * Allowed HTTP methods.
     *
     * @var string[]
     */
    protected static $METHOD_ALLOWED_VALUES = [
        RequestInterface::METHOD_CONNECT,
        RequestInterface::METHOD_DELETE,
        RequestInterface::METHOD_HEAD,
        RequestInterface::METHOD_GET,
        RequestInterface::METHOD_OPTIONS,
        RequestInterface::METHOD_PATCH,
        RequestInterface::METHOD_POST,
        RequestInterface::METHOD_PUT,
        RequestInterface::METHOD_TRACE,
    ];

    /**
     * Name of action to display.
     *
     * @var string
     */
    protected $name;

    /**
     * Url that the request will be sent to when clicking on the action.
     *
     * @var string
     */
    protected $url;

    /**
     * HTTP request method.
     *
     * @var string
     */
    protected $method;

    /**
     * The way how to render the action.
     *
     * @var string
     */
    protected $visualization;

    /**
     * BellAction constructor.
     *
     * @param string $name
     * @param string $url
     * @param string $method
     * @param string $visualization
     *
     * @throws InvalidParameterException
     */
    public function __construct(
        string $name,
        string $url,
        string $method,
        string $visualization = self::VISUALIZATION_BUTTON
    ) {
        $this->name = $name;
        $this->url = $url;
        $this->setMethod($method);
        $this->setVisualization($visualization);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getVisualization(): string
    {
        return $this->visualization;
    }

    /**
     * @param string $visualization
     */
    public function setVisualization(string $visualization): void
    {
        $this->visualization = $visualization === static::VISUALIZATION_LINK
            ? static::VISUALIZATION_LINK
            : static::VISUALIZATION_BUTTON;
    }

    /**
     * @param string $method
     *
     * @throws InvalidParameterException
     *
     * @return void
     */
    public function setMethod(string $method): void
    {
        if (!in_array(strtoupper($method), static::$METHOD_ALLOWED_VALUES)) {
            throw new InvalidParameterException(
                sprintf(
                    'HTTP method should be one of: %s',
                    print_r(static::$METHOD_ALLOWED_VALUES, true)
                )
            );
        }

        $this->method = $method;
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
            'name' => $this->name,
            'url' => $this->url,
            'method' => $this->method,
            'visualization' => $this->visualization,
        ];
    }
}
