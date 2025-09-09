<?php

namespace App\Telegram\Methods\Message;

use App\Models\User;
use App\Telegram\Types\Keyboard\KeyboardTypes;
use App\Telegram\Types\TelegramRequest\TelegramMethods;
use App\Telegram\Types\TelegramRequest\TelegramResult;

/**
 * This class represents the CopyMessage method in the Telegram Bot API.
 */
class CopyMessage extends TelegramMethods
{
    public string $methodName = "CopyMessage";

    /**
     * Required
     * Unique identifier for the target chat, username of the target channel (in the format @channelusername), or a User object
     * @var string|int|User
     */
    public string|int|User $chat_id;

    /**
     * Required
     * Unique identifier for the chat where the original message was sent (or channel username in the format @channelusername)
     * @var string|int
     */
    public string|int $from_chat_id;

    /**
     * Required
     * Message identifier in the chat specified in from_chat_id
     * @var int
     */
    public int $message_id;

    /**
     * Optional
     * Unique identifier for the target message thread (topic) of the forum; for forum supergroups only
     * @var int|null
     */
    public int $message_thread_id;

    /**
     * Optional
     * New caption for media, 0-1024 characters after entities parsing. If not specified, the original caption is kept
     * @var string|null
     */
    public string $caption;

    /**
     * Optional
     * Mode for parsing entities in the new caption. See formatting options for more details.
     * @var string|null
     */
    public string $parse_mode;

    /**
     * Optional
     * A JSON-serialized list of special entities that appear in the new caption, which can be specified instead of parse_mode
     * @var array|null
     */
    public array $caption_entities;

    /**
     * Optional
     * Pass True, if the caption must be shown above the message media. Ignored if a new caption isn't specified.
     * @var bool|null
     */
    public bool $show_caption_above_media;

    /**
     * Optional
     * Sends the message silently. Users will receive a notification with no sound.
     * @var bool|null
     */
    public bool $disable_notification;

    /**
     * Optional
     * Protects the contents of the sent message from forwarding and saving
     * @var bool|null
     */
    public bool $protect_content;

    /**
     * Optional
     * If the message is a reply, ID of the original message
     * @var string|int|null
     */
    public string|int|null $reply_to_message_id;

    public bool $disable_web_page_preview;

    /**
     * Optional
     * Additional interface options. A JSON-serialized object for an inline keyboard, custom reply keyboard, instructions to remove a reply keyboard or to force a reply from the user
     * @var KeyboardTypes|array
     */
    public KeyboardTypes|array $reply_markup = [];

    public function __construct(User|string $user = null, bool $clear = false)
    {
        //set default settings
        if ($clear) return;
        $this->parse_mode = 'HTML';
        $this->disable_web_page_preview = true;
        if (!is_null($user)) $this->chat_id = $user;
    }
}
