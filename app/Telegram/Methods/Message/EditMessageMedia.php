<?php

namespace App\Telegram\Methods\Message;

use App\Models\User;
use App\Telegram\Types\Keyboard\KeyboardTypes;
use App\Telegram\Types\TelegramRequest\TelegramMethods;

/**
 * This class represents the editMessageMedia method in the Telegram Bot API.
 */
class EditMessageMedia extends TelegramMethods
{
    public string $methodName = "EditMessageMedia";
    /**
     * Optional
     * Unique identifier of the business connection on behalf of which the message to be edited was sent
     * @var string|null
     */
    public string $business_connection_id;

    /**
     * Optional
     * Unique identifier for the target chat as a User object
     * @var User|string
     */
    public User|string $chat_id;

    /**
     * Optional
     * Identifier of the message to edit
     * @var int|null
     */
    public int $message_id;

    /**
     * Optional
     * Identifier of the inline message
     * @var string|null
     */
    public string $inline_message_id;

    /**
     * Required
     * A JSON-serialized object for a new media content of the message
     * @var array
     */
    public array $media;

    /**
     * Optional
     * A JSON-serialized object for a new inline keyboard.
     * @var KeyboardTypes|array|null
     */
    public KeyboardTypes|array|null $reply_markup = null;

    /**
     * Constructor with optional user and clear flag.
     *
     * @param User|null $user
     * @param bool $clear
     */
    public function __construct(User $user = null, bool $clear = false)
    {
        // Set default settings
        if ($clear) return;
        if (!is_null($user)) $this->chat_id = $user;
    }
}
