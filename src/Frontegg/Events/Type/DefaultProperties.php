<?php

namespace Frontegg\Events\Type;

class DefaultProperties implements DefaultPropertiesInterface
{
    /**
     * Notification title.
     *
     * @var string
     */
    protected $title;

    /**
     * Notification description.
     *
     * @var string
     */
    protected $description;

    /**
     * Additional notification properties.
     *
     * @var array
     */
    protected $additionalProperties;

    /**
     * DefaultProperties constructor.
     *
     * @param string   $title
     * @param string   $description
     * @param string[] $additionalProperties
     */
    public function __construct(
        string $title,
        string $description,
        array $additionalProperties = []
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->additionalProperties = $additionalProperties;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return array
     */
    public function getAdditionalProperties(): array
    {
        return $this->additionalProperties;
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
        $data = [];

        foreach ($this->additionalProperties as $key => $field) {
            $data[$key] = $field;
        }

        $data['title'] = $this->title;
        $data['description'] = $this->description;

        return $data;
    }
}
