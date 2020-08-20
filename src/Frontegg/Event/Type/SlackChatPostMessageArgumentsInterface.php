<?php

namespace Frontegg\Event\Type;

interface SlackChatPostMessageArgumentsInterface extends SerializableInterface
{
    public const PARSE_FULL = 'full';
    public const PARSE_NONE = 'none';

    /**
     * @return string
     */
    public function getChannel(): string;

    /**
     * @return string
     */
    public function getText(): string;

    /**
     * @return bool|null
     */
    public function getAsUser(): ?bool;

    /**
     * @return array
     */
    public function getAttachments(): array;

    /**
     * @return array
     */
    public function getBlocks(): array;

    /**
     * @return string|null
     */
    public function getIconEmoji(): ?string;

    /**
     * @return string|null
     */
    public function getIconUrl(): ?string;

    /**
     * @return bool|null
     */
    public function getLinkNames(): ?bool;

    /**
     * @return bool|null
     */
    public function getMrkdwn(): ?bool;

    /**
     * @return string
     */
    public function getParse(): string;

    /**
     * @return bool|null
     */
    public function getReplyBroadcast(): ?bool;

    /**
     * @return string|null
     */
    public function getThreadTs(): ?string;

    /**
     * @return bool|null
     */
    public function getUnfurlLinks(): ?bool;

    /**
     * @return bool|null
     */
    public function getUnfurlMedia(): ?bool;

    /**
     * @return string|null
     */
    public function getUsername(): ?string;
}