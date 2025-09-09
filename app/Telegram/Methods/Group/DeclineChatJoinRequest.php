<?php

namespace App\Telegram\Methods\Group;
use App\Models\User;
use App\Telegram\Types\Keyboard\KeyboardTypes;
use App\Telegram\Types\TelegramRequest\TelegramMethods;

/**
 * This class represents the declineChatJoinRequest method in the Telegram Bot API.
 */
class DeclineChatJoinRequest extends TelegramMethods
{
    public string $methodName = "DeclineChatJoinRequest";

    /**
     * Required
     * Unique identifier for the target chat or username of the target channel (in the format @channelusername)
     * @var string|int
     */
    public string|int $chat_id;

    /**
     * Required
     * Unique identifier of the target user
     * @var int|User
     */
    public int|User $user_id;

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
        if (!is_null($user)) $this->user_id = $user;
    }
}
