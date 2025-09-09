<?php

namespace App\Telegram\Methods\Group;

use App\Models\User;
use App\Telegram\Types\TelegramRequest\TelegramMethods;

/**
 * This class represents the unbanChatMember method in the Telegram Bot API.
 */
class UnBanChatMember extends TelegramMethods
{
    public string $methodName = "UnbanChatMember";

    /**
     * Required
     * Unique identifier for the target group or username of the target supergroup or channel (in the format @channelusername)
     * @var string|int
     */
    public string|int $chat_id;

    /**
     * Required
     * Unique identifier of the target user
     * @var User|int
     */
    public User|int $user_id;

    /**
     * Optional
     * Do nothing if the user is not banned
     * @var bool|null
     */
    public bool $only_if_banned;

    /**
     * Constructor with optional clear flag.
     *
     * @param bool $clear
     */
    public function __construct(bool $clear = false)
    {
        // Set default settings
        if ($clear) return;
    }
}
