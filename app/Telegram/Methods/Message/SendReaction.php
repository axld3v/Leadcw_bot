<?php

namespace App\Telegram\Methods\Message;

use App\Models\User;
use App\Telegram\Types\TelegramRequest\TelegramMethods;
use App\Telegram\Types\TelegramRequest\TelegramResult;

class SendReaction extends TelegramMethods
{
    public string $methodName = "setMessageReaction";
    /**
     * Unique identifier for the target chat or username of the target channel (in the format @channelusername)
     * @var User|string
     */
    public User|string $chat_id = '';

    /**
     * Identifier of the target message. If the message belongs to a media group,
     * the reaction is set to the first non-deleted message in the group instead.
     * @var int
     */
    public int $message_id;

    /**
     * A JSON-serialized list of reaction types to set on the message.
     * A custom emoji reaction can be used if it is either already present on the message or explicitly allowed by chat administrators.
     * @var string|array
     */
    public string|array $reaction = [];

    public function __construct(User|string $user = null, bool $clear = false)
    {
        //set default settings
        if ($clear) return;
        if (!is_null($user)) $this->chat_id = $user;
    }

    public function formatting(): void
    {
        if (is_string($this->reaction))
            $this->reaction = [['type' => 'emoji', 'emoji' => $this->reaction]];
    }
}
