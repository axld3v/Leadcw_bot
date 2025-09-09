<?php

namespace App\Telegram\Methods\Message;

use App\Models\User;
use App\Telegram\Types\TelegramRequest\TelegramMethods;
use App\Telegram\Types\TelegramRequest\TelegramResult;

class sendChatAction extends TelegramMethods
{
    public string $methodName = "sendChatAction";
    /**
     * Unique identifier for the target chat or username of the target channel (in the format @channelusername)
     * @var User|string
     */
    public User|string $chat_id = '';

    public string $business_connection_id;

    /**
     * Type of action to broadcast. Choose one, depending on what the user is about to receive: typing for text messages, upload_photo
     * @var string
     */
    public string $action;
    public int $message_thread_id;

    public function __construct(User|string $user = null, bool $clear = false)
    {
        //set default settings
        if ($clear) return;
        if (!is_null($user)) $this->chat_id = $user;
    }
}
