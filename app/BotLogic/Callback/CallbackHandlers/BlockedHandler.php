<?php

namespace App\BotLogic\Callback\CallbackHandlers;

use App\BotLogic\Callback\BaseCallbackHandler;
use App\DTO\UserDTO;

class BlockedHandler extends BaseCallbackHandler
{
    public static function handle(UserDTO &$userDTO, \App\Telegram\Types\Update\Callback $update): bool
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
