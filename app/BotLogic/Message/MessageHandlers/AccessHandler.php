<?php

namespace App\BotLogic\Message\MessageHandlers;

use App\BotLogic\Message\BaseMessageHandler;
use App\BotLogic\Message\MessageHandlers\Business\BusinessHandler;
use App\BotLogic\Message\MessageHandlers\Group\GroupHandler;
use App\DTO\UserDTO;
use App\Telegram\Types\Update\Message;

class AccessHandler extends BaseMessageHandler
{
    public static function handle(UserDTO &$userDTO, Message $update): bool
    {
        try
        {
            if (isset($update->jsonData["left_chat_member"])) return true;
            if (isset($update->jsonData["new_chat_member"])) return true;
            if ($update->isBotMessage) return true;

            if ($userDTO->user->blocked) return BlockedHandler::handle($userDTO, $update); //проверка на блокировку
            if ($userDTO->update->isGroup) return GroupHandler::handle($userDTO, $update);
            if ($userDTO->update->isBusiness) return BusinessHandler::handle($userDTO, $update);
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return false;
    }
}
