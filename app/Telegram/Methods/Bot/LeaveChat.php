<?php

namespace App\Telegram\Methods\Bot;

use App\Telegram\Types\TelegramRequest\TelegramMethods;

/**
 * This class represents the leaveChat method in the Telegram Bot API.
 */
class LeaveChat extends TelegramMethods
{
    public string $methodName = "LeaveChat";

    /**
     * Required
     * Unique identifier for the target chat or username of the target supergroup or channel (in the format @channelusername)
     * @var string|int
     */
    public string|int $chat_id;

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
