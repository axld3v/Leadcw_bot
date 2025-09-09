<?php

namespace App\Telegram\Methods\Group;

use App\Models\User;
use App\Telegram\Types\Keyboard\KeyboardTypes;
use App\Telegram\Types\TelegramRequest\TelegramMethods;

/**
 * This class represents the banChatMember method in the Telegram Bot API.
 */
class BanChatMember extends TelegramMethods
{
    public string $methodName = "BanChatMember";

    /**
     * Required
     * Unique identifier for the target group or username of the target supergroup or channel (in the format @channelusername)
     * @var string|int
     */
    public string|int $chat_id;

    /**
     * Required
     * Unique identifier of the target user
     * @var int|User
     */
    public User|int $user_id;

    /**
     * Optional
     * Date when the user will be unbanned; Unix time. If user is banned for more than 366 days or less than 30 seconds from the current time they are considered to be banned forever. Applied for supergroups and channels only.
     * @var int|null
     */
    public int $until_date;

    /**
     * Optional
     * Pass True to delete all messages from the chat for the user that is being removed. If False, the user will be able to see messages in the group that were sent before the user was removed. Always True for supergroups and channels.
     * @var bool|null
     */
    public bool $revoke_messages;

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
