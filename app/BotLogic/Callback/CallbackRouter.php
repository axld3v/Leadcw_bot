<?php

namespace App\BotLogic\Callback;

use App\BotLogic\Callback\CallbackHandlers\AccessHandler;
use App\BotLogic\Callback\CallbackHandlers\Chat\Admin\AdminRouterHandler;
use App\BotLogic\Callback\CallbackHandlers\Chat\StartHandler;
use App\DTO\UserDTO;

class CallbackRouter
{
    private static array $handlers = [
        //Обработчики сообщений в нужном порядке
        AccessHandler::class,
        StartHandler::class,
        AdminRouterHandler::class,
    ];

    public static function handle(UserDTO &$userDTO): void
    {
        $status_send = false;
        try
        {
            foreach (self::$handlers as $handler)
            {
                if ($handler::handle($userDTO, $userDTO->update->getTypeCallback()))
                {
                    $status_send = true;
                    break;
                }
            }
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
    }
}
