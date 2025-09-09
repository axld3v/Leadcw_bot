<?php

namespace App\Telegram\Methods\Message;

use App\Models\User;
use App\Telegram\Types\Keyboard\KeyboardTypes;
use App\Telegram\Types\TelegramRequest\TelegramMethods;

/**
 * This class represents the unpinChatMessage method in the Telegram Bot API.
 */
class UnpinChatMessage extends TelegramMethods
{
    public string $methodName = "UnpinChatMessage";

    /**
     * Required
     * Unique identifier for the target chat or username of the target channel (in the format @channelusername)
     * @var string|int|User
     */
    public string|int|User $chat_id;

    /**
     * Optional
     * Identifier of the message to unpin. Required if business_connection_id is specified. If not specified, the most recent pinned message (by sending date) will be unpinned.
     * @var int|null
     */
    public int $message_id;

    /**
     * Optional
     * Unique identifier of the business connection on behalf of which the message will be unpinned
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
