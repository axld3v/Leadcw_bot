<?php

namespace App\Telegram\Methods\Topic;

use App\Telegram\Types\TelegramRequest\TelegramMethods;
use App\Telegram\Types\TelegramRequest\TelegramResult;

class CreateForumTopic extends TelegramMethods
{
    public string $methodName = "CreateForumTopic";
    /**
     * Required
     * Unique identifier for the target chat or username of the target channel (in the format @channelusername)
     * @var string
     */
    public string $chat_id = '';

    /**
     * Required
     * Topic name, 1-128 characters
     * @var string
     */
    public string $name = '';

    /**
     * Color of the topic icon in RGB format. Currently, must be one of 7322096 (0x6FB9F0
     * @var int
     */
    public int $icon_color = 0;

    /**
     * Unique identifier of the custom emoji shown as the topic icon
     * @var string
     */
    public string $icon_custom_emoji_id = '';


    public function __construct(string $user = null, bool $clear = false)
    {
        //set default settings
        if ($clear) return;
        if (!is_null($user)) $this->chat_id = $user;
    }

    static function result(TelegramResult $result)
    {
        // TODO: Implement result() method.
    }

    public function formatting(): void
    {
        $this->name = mb_substr($this->name, 0, 120);
    }
}
