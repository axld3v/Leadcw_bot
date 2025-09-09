<?php

namespace App\BotLogic;

use App\BotLogic\Callback\CallbackRouter;
use App\BotLogic\Message\MessageRouter;
use App\DTO\UserDTO;

class EventRouterAction
{
    public static function handle(UserDTO $userDTO): void
    {
        try
        {
            if (is_null($userDTO->user) || is_null($userDTO->update)) return;
            $typeAction = $userDTO->update->type;
            switch (mb_strtolower(trim($typeAction)))
            {
                case "business_message":
                case "message":
                    MessageRouter::handle($userDTO);
                    break;
                case "callback":
                    CallbackRouter::handle($userDTO);
                    break;
            }
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
    }
}
