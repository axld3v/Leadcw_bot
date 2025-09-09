<?php

namespace App\Telegram\Methods\Topic;

use App\Models\User;
use App\Telegram\Types\Keyboard\KeyboardTypes;
use App\Telegram\Types\TelegramRequest\TelegramMethods;

/**
 * This class represents the closeForumTopic method in the Telegram Bot API.
 */
class CloseForumTopic extends TelegramMethods
{
    public string $methodName = "CloseForumTopic";

    /**
     * Required
     * Unique identifier for the target chat or username of the target supergroup (in the format @supergroupusername)
     * @var string|int
     */
    public string|int $chat_id;

    /**
     * Required
     * Unique identifier for the target message thread of the forum topic
     * @var int
     */
    public int $message_thread_id;

    /**
     * Constructor with optional clear flag.
     *
     * @param bool $clear
     */
    public function __construct( bool $clear = false)
    {
        // Set default settings
        if ($clear) return;
    }
}
