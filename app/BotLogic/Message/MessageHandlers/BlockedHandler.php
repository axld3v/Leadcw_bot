<?php

namespace App\BotLogic\Message\MessageHandlers;

use App\BotLogic\Message\BaseMessageHandler;
use App\DTO\UserDTO;
use App\Telegram\Types\Update\Message;

class BlockedHandler extends BaseMessageHandler
{
    public static function handle(UserDTO &$userDTO, Message $update): bool
    {
        try
        {
            return $userDTO->user->blocked;
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return false;
    }
}
