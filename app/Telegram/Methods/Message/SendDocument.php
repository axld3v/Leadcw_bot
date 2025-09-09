<?php

namespace App\Telegram\Methods\Message;

use App\Models\User;
use App\Telegram\Types\Keyboard\KeyboardTypes;
use App\Telegram\Types\TelegramRequest\TelegramMethods;

/**
 * This class represents the SendDocument method in the Telegram Bot API.
 */
class SendDocument extends TelegramMethods
{
    public string $methodName = "SendDocument";

    /**
     * Required
     * Unique identifier for the target chat, username of the target channel (in the format @channelusername), or a User object
     * @var string|int|User
     */
    public string|int|User $chat_id;

    /**
     * Optional
     * Unique identifier for the target message thread (topic) of the forum; for forum supergroups only
     * @var int|null
     */
    public int $message_thread_id;

    /**
     * Required
     * File to send. Pass a file_id as String to send a file that exists on the Telegram servers (recommended),
     * pass an HTTP URL as a String for Telegram to get a file from the Internet, or upload a new one using multipart/form-data.
     * @var string
     */
    public string $document;

    /**
     * Optional
     * Thumbnail of the file sent; can be ignored if thumbnail generation for the file is supported server-side.
     * The thumbnail should be in JPEG format and less than 200 kB in size. A thumbnail's width and height should not exceed 320.
     * Ignored if the file is not uploaded using multipart/form-data.
     * @var string|null
     */
    public string $thumbnail;

    /**
     * Optional
     * Document caption (may also be used when resending documents by file_id), 0-1024 characters after entities parsing
     * @var string|null
     */
    public string $caption;

    /**
     * Optional
     * Mode for parsing entities in the document caption. See formatting options for more details.
     * @var string|null
     */
    public string $parse_mode;

    /**
     * Optional
     * A JSON-serialized list of special entities that appear in the caption, which can be specified instead of parse_mode
     * @var array|null
     */
    public array $caption_entities;

    /**
     * Optional
     * Disables automatic server-side content type detection for files uploaded using multipart/form-data
     * @var bool|null
     */
    public bool $disable_content_type_detection;

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
     * Unique identifier of the message effect to be added to the message; for private chats only
     * @var string|null
     */
    public string $message_effect_id;

    /**
     * Optional
     * If the message is a reply, ID of the original message
     * @var string|int|null
     */
    public string|int $reply_to_message_id;

    /**
     * Optional
     * Additional interface options. A JSON-serialized object for an inline keyboard, custom reply keyboard, instructions to remove a reply keyboard or to force a reply from the user
     * @var KeyboardTypes|array
     */
    public KeyboardTypes|array $reply_markup = [];

    /**
     * Optional
     * Unique identifier of the business connection on behalf of which the message will be sent
     * @var string|null
     */
    public string $business_connection_id;

    /**
     * Constructor with optional user and clear flag.
     *
     * @param User|string|null $user
     * @param bool $clear
     */
    public function __construct(User|string $user = null, bool $clear = false)
    {
        // Set default settings
        if ($clear) return;
        $this->parse_mode = 'HTML';
        if (!is_null($user)) $this->chat_id = $user;
    }
}
