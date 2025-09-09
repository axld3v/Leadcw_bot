<?php

namespace App\BotLogic\Message;

use App\BotLogic\Message\MessageHandlers\AccessHandler;
use App\BotLogic\Message\MessageHandlers\Chat\AddInfoHandler;
use App\BotLogic\Message\MessageHandlers\Chat\Admin\AdminRouterHandler;
use App\BotLogic\Message\MessageHandlers\Chat\StartHandler;
use App\DTO\UserDTO;

class MessageRouter
{
    private static array $handlers = [
        //Обработчики сообщений в нужном порядке
        AccessHandler::class,
        AdminRouterHandler::class,
        StartHandler::class,
        AddInfoHandler::class
    ];

    public static function handle(UserDTO &$userDTO): void
    {
        $status_send = false;
        try
        {
            foreach (self::$handlers as $handler)
            {
                if ($handler::handle($userDTO, $userDTO->update->getTypeMessage()))
                {
                    $status_send = true;
                    break;
                }
            }
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        /* if (!$status_send)
             UnknownMessageHandler::handle($userDTO, $userDTO->update->getTypeMessage());*/
    }
}
