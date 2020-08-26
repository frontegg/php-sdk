<?php

namespace Frontegg\Events\Channel;

class WebPushProperties implements WebPushPropertiesInterface
{
    /**
     * Web push notification title.
     *
     * @var string|null
     */
    protected $title;

    /**
     * Web push notification body.
     *
     * @var string|null
     */
    protected $body;

    /**
     * Send web push only to one user.
     *
     * @var string|null
     */
    protected $userId;

    /**
     * WebPushProperties constructor.
     *
     * @param string|null $title
     * @param string|null $body
     * @param string|null $userId
     */
    public function __construct(
        ?string $title = null,
        ?string $body = null,
        ?string $userId = null
    ) {
        $this->title = $title;
        $this->body = $body;
        $this->userId = $userId;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @return string|null
     */
    public function getUserId(): ?string
    {
        return $this->userId;
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
            'title' => $this->title,
            'body' => $this->body,
            'userId' => $this->userId,
        ];
    }
}
