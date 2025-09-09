<?php

namespace App\BotLogic\Callback\CallbackHandlers;

use App\BotLogic\Callback\BaseCallbackHandler;
use App\BotLogic\Callback\CallbackHandlers\Group\GroupHandler;
use App\DTO\UserDTO;

class AccessHandler extends BaseCallbackHandler
{
    public static function handle(UserDTO &$userDTO, \App\Telegram\Types\Update\Callback $update): bool
    {
        try
        {
            if ($userDTO->user->blocked) return BlockedHandler::handle($userDTO, $update); //проверка на блокировку
            if ($userDTO->update->isGroup) return GroupHandler::handle($userDTO, $update);
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return false;
    }
}
