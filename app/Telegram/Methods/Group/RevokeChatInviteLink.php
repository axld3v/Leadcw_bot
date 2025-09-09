<?php

namespace App\Telegram\Methods\Group;

use App\Models\User;
use App\Telegram\Types\Keyboard\KeyboardTypes;
use App\Telegram\Types\TelegramRequest\TelegramMethods;

/**
 * This class represents the revokeChatInviteLink method in the Telegram Bot API.
 */
class RevokeChatInviteLink extends TelegramMethods
{
    public string $methodName = "RevokeChatInviteLink";

    /**
     * Required
     * Unique identifier for the target chat or username of the target channel (in the format @channelusername)
     * @var string|int
     */
    public string|int $chat_id;

    /**
     * Required
     * The invite link to revoke
     * @var string
     */
    public string $invite_link;

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
