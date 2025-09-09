<?php

namespace App\BotLogic\Callback\CallbackHandlers\Chat\Admin;

use App\BotLogic\Callback\BaseCallbackHandler;
use App\DTO\UserDTO;

class AdminRouterHandler extends BaseCallbackHandler
{
    private static array $handlers = [
        //Обработчики кнопок в нужном порядке
    ];

    public static function handle(UserDTO &$userDTO, \App\Telegram\Types\Update\Callback $update): bool
    {
        try
        {
            if ($update->button == "mainadmin")
            {
                \App\BotLogic\Message\MessageHandlers\Chat\Admin\AdminRouterHandler::sendAdmin($userDTO);
                return true;
            }

            $status_send = false;
            foreach (self::$handlers as $handler)
            {
                if ($handler::handle($userDTO, $userDTO->update->getTypeCallback()))
                {
                    $status_send = true;
                    break;
                }
            }
            return $status_send;
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return false;
    }
}
