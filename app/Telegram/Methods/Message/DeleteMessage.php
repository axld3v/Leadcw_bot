<?php

namespace App\Telegram\Methods\Message;

use App\Models\User;
use App\Telegram\Types\TelegramRequest\TelegramMethods;
use App\Telegram\Types\TelegramRequest\TelegramResult;

class DeleteMessage extends TelegramMethods
{
    public string $methodName = "DeleteMessage";
    /**
     * Required
     * Unique identifier for the target chat or username of the target channel (in the format @channelusername)
     * @var string|User
     */
    public string|User $chat_id = '';

    /**
     * Required
     * Identifier of the message to delete
     * @var string
     */
    public string $message_id = '';


    public function __construct(User|string $user = null, bool $clear = false)
    {
        //set default settings
        if ($clear) return;
        if (!is_null($user)) $this->chat_id = $user;
    }
}
