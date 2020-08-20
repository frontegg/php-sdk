<?php

namespace Frontegg\Event\Type;

class WebHookBody implements SerializableInterface
{
    /**
     * Data to store in format key-value.
     *
     * @var array
     */
    protected $data;

    /**
     * WebHookBody constructor.
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Returns value by field name.
     *
     * @param string $field
     *
     * @return mixed|null
     */
    public function getValue(string $field)
    {
        return $this->data[$field] ?? null;
    }

    /**
     * Sets value for specified field name.
     *
     * @param string $field
     * @param        $value
     *
     * @return void
     */
    public function setValue(string $field, $value): void
    {
        $this->data[$field] = $value;
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

        foreach ($this->data as $key => $field) {
            $data[$key] = $field;
        }

        return $data;
    }
}