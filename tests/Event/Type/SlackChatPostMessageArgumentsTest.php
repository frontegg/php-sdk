<?php

namespace Frontegg\Tests\Event\Type;

use Frontegg\Event\Type\SerializableInterface;
use Frontegg\Event\Type\SlackChatPostMessageArguments;
use Frontegg\Event\Type\SlackChatPostMessageArgumentsInterface;
use PHPUnit\Framework\TestCase;

class SlackChatPostMessageArgumentsTest extends TestCase
{
    /**
     * @return void
     */
    public function testSlackChatPostMessageArgumentsCanBeCreated(): void
    {
        // Act
        $object = new SlackChatPostMessageArguments(
            'SLACK-API-TOKEN',
            '#general',
            'Some text to show!',
            false,
            [],
            [],
            null,
            null,
            false,
            false,
            'none',
            false,
            null,
            false,
            false,
            'test chat bot'
        );

        // Assert
        $this->assertInstanceOf(
            SlackChatPostMessageArgumentsInterface::class,
            $object
        );
        $this->assertEquals('#general', $object->getChannel());
        $this->assertEquals('Some text to show!', $object->getText());
        $this->assertEquals([], $object->getAttachments());
        $this->assertEquals([], $object->getBlocks());
        $this->assertNull($object->getIconEmoji());
        $this->assertNull($object->getIconUrl());
        $this->assertEquals(false, $object->getAsUser());
        $this->assertEquals('none', $object->getParse());
        $this->assertEquals(false, $object->getReplyBroadcast());
        $this->assertNull($object->getThreadTs());
        $this->assertEquals(false, $object->getUnfurlLinks());
        $this->assertEquals(false, $object->getUnfurlMedia());
        $this->assertEquals('test chat bot', $object->getUsername());
    }

    /**
     * @return void
     */
    public function testSlackChatPostMessageArgumentsCanBeSerialized(): void
    {
        // Arrange
        $object = new SlackChatPostMessageArguments(
            'SLACK-API-TOKEN',
            '#general',
            'Some text to show!',
            false,
            [],
            [],
            null,
            null,
            false,
            false,
            'none',
            false,
            null,
            false,
            false,
            'test chat bot'
        );

        // Act
        $json = $object->toJSON();

        // Assert
        $this->assertInstanceOf(SerializableInterface::class, $object);
        $this->assertJsonStringEqualsJsonString(
            '{
                "token": "SLACK-API-TOKEN",
                "channel": "#general",
                "text": "Some text to show!",
                "as_user": false,
                "attachments": {},
                "blocks": {},
                "icon_emoji": null,
                "icon_url": null,
                "link_names": false,
                "mrkdwn": false,
                "parse": "none",
                "reply_broadcast": false,
                "thread_ts": null,
                "unfurl_links": false,
                "unfurl_media": false,
                "username": "test chat bot"
            }',
            $json
        );
    }

    /**
     * @return void
     */
    public function testEmptySlackChatPostMessageArgumentsCanBeSerialized(): void
    {
        // Arrange
        $object = new SlackChatPostMessageArguments(
            'SLACK-API-TOKEN',
            '#general',
            'Some text to show!'
        );

        // Act
        $json = $object->toJSON();

        // Assert
        $this->assertInstanceOf(SerializableInterface::class, $object);
        $this->assertJsonStringEqualsJsonString(
            '{
                "token": "SLACK-API-TOKEN",
                "channel": "#general",
                "text": "Some text to show!",
                "as_user": null,
                "attachments": {},
                "blocks": {},
                "icon_emoji": null,
                "icon_url": null,
                "link_names": null,
                "mrkdwn": null,
                "parse": "none",
                "reply_broadcast": null,
                "thread_ts": null,
                "unfurl_links": null,
                "unfurl_media": null,
                "username": null
            }',
            $json
        );
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
