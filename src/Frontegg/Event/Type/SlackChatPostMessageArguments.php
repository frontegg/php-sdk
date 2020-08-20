<?php

namespace Frontegg\Event\Type;

class SlackChatPostMessageArguments implements SlackChatPostMessageArgumentsInterface
{
    /**
     * Slack API token.
     *
     * @var string|null
     */
    protected $token;

    /**
     * Channel name.
     *
     * @var string
     */
    protected $channel;

    /**
     * Message text.
     *
     * @var string
     */
    protected $text;

    /**
     * Post message like user or not.
     *
     * @var bool|null
     */
    protected $asUser;

    /**
     * @TODO: Implement items to be instances of "MessageAttachment" class.
     *
     * Message attachments.
     *
     * @var array
     */
    protected $attachments;

    /**
     * @TODO: Implement items to be instances of "KnownBlock" or "Block" class.
     *
     * @var array
     */
    protected $blocks;

    /**
     * @var string|null
     */
    protected $iconEmoji;

    /**
     * @var string|null
     */
    protected $iconUrl;

    /**
     * @var bool|null
     */
    protected $linkNames;

    /**
     * @var bool|null
     */
    protected $mrkdwn;

    /**
     * Can be 'full' or 'none'.
     *
     * @var string
     */
    protected $parse;

    /**
     * @var bool|null
     */
    protected $replyBroadcast;

    /**
     * @var string|null
     */
    protected $threadTs;

    /**
     * @var bool|null
     */
    protected $unfurlLinks;

    /**
     * @var bool|null
     */
    protected $unfurlMedia;

    /**
     * @var string|null
     */
    protected $username;

    /**
     * SlackChatPostMessageArguments constructor.
     *
     * @param string      $token
     * @param string      $channel
     * @param string      $text
     * @param bool|null   $asUser
     * @param array       $attachments
     * @param array       $blocks
     * @param string|null $iconEmoji
     * @param string|null $iconUrl
     * @param bool|null   $linkNames
     * @param bool|null   $mrkdwn
     * @param string      $parse
     * @param bool|null   $replyBroadcast
     * @param string|null $threadTs
     * @param bool|null   $unfurlLinks
     * @param bool|null   $unfurlMedia
     * @param string|null $username
     */
    public function __construct(
        string $token,
        string $channel,
        string $text,
        ?bool $asUser = null,
        array $attachments = [],
        array $blocks = [],
        ?string $iconEmoji = null,
        ?string $iconUrl = null,
        ?bool $linkNames = null,
        ?bool $mrkdwn = null,
        string $parse = self::PARSE_NONE,
        ?bool $replyBroadcast = null,
        ?string $threadTs = null,
        ?bool $unfurlLinks = null,
        ?bool $unfurlMedia = null,
        ?string $username = null
    ) {
        $this->token = $token;
        $this->channel = $channel;
        $this->text = $text;
        $this->asUser = $asUser;
        $this->attachments = $attachments;
        $this->blocks = $blocks;
        $this->iconEmoji = $iconEmoji;
        $this->iconUrl = $iconUrl;
        $this->linkNames = $linkNames;
        $this->mrkdwn = $mrkdwn;
        $this->setParse($parse);
        $this->replyBroadcast = $replyBroadcast;
        $this->threadTs = $threadTs;
        $this->unfurlLinks = $unfurlLinks;
        $this->unfurlMedia = $unfurlMedia;
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getChannel(): string
    {
        return $this->channel;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return bool|null
     */
    public function getAsUser(): ?bool
    {
        return $this->asUser;
    }

    /**
     * @return array
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * @return array
     */
    public function getBlocks(): array
    {
        return $this->blocks;
    }

    /**
     * @return string|null
     */
    public function getIconEmoji(): ?string
    {
        return $this->iconEmoji;
    }

    /**
     * @return string|null
     */
    public function getIconUrl(): ?string
    {
        return $this->iconUrl;
    }

    /**
     * @return bool|null
     */
    public function getLinkNames(): ?bool
    {
        return $this->linkNames;
    }

    /**
     * @return bool|null
     */
    public function getMrkdwn(): ?bool
    {
        return $this->mrkdwn;
    }

    /**
     * @return string
     */
    public function getParse(): string
    {
        return $this->parse;
    }

    /**
     * @return bool|null
     */
    public function getReplyBroadcast(): ?bool
    {
        return $this->replyBroadcast;
    }

    /**
     * @return string|null
     */
    public function getThreadTs(): ?string
    {
        return $this->threadTs;
    }

    /**
     * @return bool|null
     */
    public function getUnfurlLinks(): ?bool
    {
        return $this->unfurlLinks;
    }

    /**
     * @return bool|null
     */
    public function getUnfurlMedia(): ?bool
    {
        return $this->unfurlMedia;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string $parse
     */
    public function setParse(string $parse): void
    {
        $this->parse = $parse === static::PARSE_FULL
            ? static::PARSE_FULL
            : static::PARSE_NONE;
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
            'token' => $this->token,
            'channel' => $this->channel,
            'text' => $this->text,
            'as_user' => $this->asUser,
            'attachments' => $this->attachments, // @TODO: Refactor
            'blocks' => $this->blocks, // @TODO: Refactor
            'icon_emoji' => $this->iconEmoji,
            'icon_url' => $this->iconUrl,
            'link_names' => $this->linkNames,
            'mrkdwn' => $this->mrkdwn,
            'parse' => $this->parse,
            'reply_broadcast' => $this->replyBroadcast,
            'thread_ts' => $this->threadTs,
            'unfurl_links' => $this->unfurlLinks,
            'unfurl_media' => $this->unfurlMedia,
            'username' => $this->username,
        ];
    }
}

//export interface ChatPostMessageArguments extends WebAPICallOptions, TokenOverridable {
//    channel: string;
//    text: string;
//    as_user?: boolean;
//    attachments?: MessageAttachment[];
//    blocks?: (KnownBlock | Block)[];
//    icon_emoji?: string;
//    icon_url?: string;
//    link_names?: boolean;
//    mrkdwn?: boolean;
//    parse?: 'full' | 'none';
//    reply_broadcast?: boolean;
//    thread_ts?: string;
//    unfurl_links?: boolean;
//    unfurl_media?: boolean;
//    username?: string;
//}

//export interface WebAPICallOptions {
//    [argument: string]: unknown;
//}

//export interface TokenOverridable {
//    token?: string;
//}
