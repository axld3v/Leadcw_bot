<?php

namespace App\BotLogic\Callback\CallbackHandlers\Group;

use App\BotLogic\Callback\BaseCallbackHandler;
use App\DTO\UserDTO;

class GroupHandler extends BaseCallbackHandler
{
    public static function handle(UserDTO &$userDTO, \App\Telegram\Types\Update\Callback $update): bool
    {
        try
        {
            return true; //Заблокировать использование бота в группах
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return true;
    }
}
