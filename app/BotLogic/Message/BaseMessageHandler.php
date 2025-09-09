<?php

namespace App\BotLogic\Message;

use App\DTO\UserDTO;
use App\Telegram\Types\Update\Message;

abstract class BaseMessageHandler
{
    public abstract static function handle(UserDTO &$userDTO, Message $update): bool;
}
