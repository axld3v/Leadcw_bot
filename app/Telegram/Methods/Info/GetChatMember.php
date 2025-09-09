<?php

namespace App\Telegram\Methods\Info;

use App\Models\User;
use App\Telegram\Types\TelegramRequest\TelegramMethods;
/**
 * This class represents the getChatMember method in the Telegram Bot API.
 */
class GetChatMember extends TelegramMethods
{
    public string $methodName = "GetChatMember";

    /**
     * Required
     * Unique identifier for the target chat or username of the target supergroup or channel (in the format @channelusername)
     * @var string|int
     */
    public string|int $chat_id;

    /**
     * Required
     * Unique identifier of the target user
     * @var int|User
     */
    public int|User $user_id;

    /**
     * Constructor with optional clear flag.
     *
     * @param User|string|null $user
     * @param bool $clear
     */
    public function __construct(User|string $user = null, bool $clear = false)
    {
        // Set default settings
        if ($clear) return;
        if (!is_null($user)) $this->user_id = $user;
    }
}
