<?php

namespace App\BotLogic\Callback;

use App\DTO\UserDTO;
use App\Telegram\Types\Update\Callback;

abstract class BaseCallbackHandler
{
    public abstract static function handle(UserDTO &$userDTO, Callback $update): bool;
}
