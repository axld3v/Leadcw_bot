<?php

namespace App\BotLogic\Message\MessageHandlers\Business;

use App\BotLogic\Message\BaseMessageHandler;
use App\DTO\UserDTO;
use App\Telegram\Types\Update\Message;

class BusinessHandler extends BaseMessageHandler
{
    public static function handle(UserDTO &$userDTO, Message $update): bool
    {
        try
        {
            return true;
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return true;
    }
}
