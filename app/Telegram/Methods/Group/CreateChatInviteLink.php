<?php

namespace App\Telegram\Methods\Group;

use App\Models\User;
use App\Telegram\Types\Keyboard\KeyboardTypes;
use App\Telegram\Types\TelegramRequest\TelegramMethods;

/**
 * This class represents the createChatInviteLink method in the Telegram Bot API.
 */
class CreateChatInviteLink extends TelegramMethods
{
    public string $methodName = "CreateChatInviteLink";

    /**
     * Required
     * Unique identifier for the target chat or username of the target channel (in the format @channelusername)
     * @var string|int
     */
    public string|int $chat_id;

    /**
     * Optional
     * Invite link name; 0-32 characters
     * @var string|null
     */
    public string $name;

    /**
     * Optional
     * Point in time (Unix timestamp) when the link will expire
     * @var int|null
     */
    public int $expire_date;

    /**
     * Optional
     * The maximum number of users that can be members of the chat simultaneously after joining the chat via this invite link; 1-99999
     * @var int|null
     */
    public int $member_limit;

    /**
     * Optional
     * True, if users joining the chat via the link need to be approved by chat administrators. If True, member_limit can't be specified
     * @var bool|null
     */
    public bool $creates_join_request ;

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
