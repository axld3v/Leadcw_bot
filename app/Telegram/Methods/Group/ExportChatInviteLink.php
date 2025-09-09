<?php

namespace App\Telegram\Methods\Group;

use App\Models\User;
use App\Telegram\Types\Keyboard\KeyboardTypes;
use App\Telegram\Types\TelegramRequest\TelegramMethods;

/**
 * This class represents the exportChatInviteLink method in the Telegram Bot API.
 */
class ExportChatInviteLink extends TelegramMethods
{
    public string $methodName = "ExportChatInviteLink";

    /**
     * Required
     * Unique identifier for the target chat or username of the target channel (in the format @channelusername)
     * @var string|int
     */
    public string|int $chat_id;

    /**
     * Constructor with optional clear flag.
     * @param bool $clear
     */
    public function __construct(bool $clear = false)
    {
        // Set default settings
        if ($clear) return;
    }
}
