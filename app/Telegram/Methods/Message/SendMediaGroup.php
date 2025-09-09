<?php

namespace App\Telegram\Methods\Message;

use App\Models\User;
use App\Telegram\Types\TelegramRequest\TelegramMethods;
use App\Telegram\Types\TelegramRequest\TelegramResult;

class SendMediaGroup extends TelegramMethods
{
    public string $methodName = "SendMediaGroup";
    /**
     * Unique identifier for the target chat or username of the target channel (in the format @channelusername)
     * @var string|User
     */
    public string|User $chat_id = '';

    /**
     * Unique identifier for the target chat or username of the target channel (in the format @channelusername)
     * @var array
     */
    public array $media;

    /**
     * Optional. Unique identifier for the target message thread (topic) of the forum; for forum supergroups only
     * @var int
     */
    public int $message_thread_id;

    /**
     * Unique identifier of the message effect to be added to the message; for private chats only
     * @var int
     */
    public int $message_effect_id;

    /**
     * Unique identifier of the business connection on behalf of which the message will be sent
     * @var string
     */
    public string $business_connection_id;

    /**
     * Sends messages silently. Users will receive a notification with no sound.
     * @var bool
     */
    public bool $disable_notification;

    /**
     * Protects the contents of the sent messages from forwarding and saving
     * @var bool
     */
    public bool $protect_content;


    public function __construct(User|string $user = null, bool $clear = false)
    {
        //set default settings
        if ($clear) return;
        $this->parse_mode = 'HTML';
        if (!is_null($user)) $this->chat_id = $user;
    }
}
