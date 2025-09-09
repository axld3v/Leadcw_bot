<?php

namespace App\Telegram\Methods\Message;

use App\Models\User;
use App\Telegram\Types\Keyboard\KeyboardTypes;
use App\Telegram\Types\TelegramRequest\TelegramMethods;

/**
 * This class represents the pinChatMessage method in the Telegram Bot API.
 */
class PinChatMessage extends TelegramMethods
{
    public string $methodName = "PinChatMessage";

    /**
     * Required
     * Unique identifier for the target chat or username of the target channel (in the format @channelusername)
     * @var string|int|User
     */
    public string|int|User $chat_id;

    /**
     * Required
     * Identifier of a message to pin
     * @var int
     */
    public int $message_id;

    /**
     * Optional
     * Pass True if it is not necessary to send a notification to all chat members about the new pinned message. Notifications are always disabled in channels and private chats.
     * @var bool
     */
    public bool $disable_notification;

    /**
     * Optional
     * Unique identifier of the business connection on behalf of which the message will be pinned
     * @var string|null
     */
    public string $business_connection_id;

    /**
     * Constructor with optional clear flag.
     *
     * @param User|string|null $user
     * @param bool $clear
     */
    public function __construct(User|string|null $user = null, bool $clear = false)
    {
        // Set default settings
        if ($clear) return;
        if (!is_null($user)) $this->chat_id = $user;
    }
}
