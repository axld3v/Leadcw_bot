<?php

namespace App\BotLogic\Message\MessageHandlers;

use App\BotLogic\Message\BaseMessageHandler;
use App\BotLogic\Message\MessageHandlers\Chat\StartHandler;
use App\DTO\UserDTO;
use App\Telegram\Types\Update\Message;

class UnknownMessageHandler extends BaseMessageHandler
{
    public static function handle(UserDTO &$userDTO, Message $update): bool
    {
        return true;
    }
}
