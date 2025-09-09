<?php

namespace App\Telegram\Methods\Topic;

use App\Models\User;
use App\Telegram\Types\Keyboard\KeyboardTypes;
use App\Telegram\Types\TelegramRequest\TelegramMethods;

/**
 * This class represents the editForumTopic method in the Telegram Bot API.
 */
class EditForumTopic extends TelegramMethods
{
    public string $methodName = "EditForumTopic";

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
     * Optional
     * New topic name, 0-128 characters. If not specified or empty, the current name of the topic will be kept
     * @var string|null
     */
    public string $name;

    /**
     * Optional
     * New unique identifier of the custom emoji shown as the topic icon. Use getForumTopicIconStickers to get all allowed custom emoji identifiers.
     * Pass an empty string to remove the icon. If not specified, the current icon will be kept
     * @var string|null
     */
    public string $icon_custom_emoji_id ;

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
