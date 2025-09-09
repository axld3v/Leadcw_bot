<?php

namespace App\BotLogic\Message\MessageHandlers\Chat\Admin;

use App\BotLogic\Message\BaseMessageHandler;
use App\BotLogic\Methods\lib\AdminMethods\IsAdminSheets;
use App\BotLogic\Methods\UserMethods\UserClearFields;
use App\DTO\UserDTO;
use App\Telegram\Methods\Message\SendMessage;
use App\Telegram\Types\Keyboard\KeyboardReply;
use App\Telegram\Types\Update\Message;

class AdminRouterHandler extends BaseMessageHandler
{
    public static array $adminKeyboard = [
    ];
    private static array $handlers = [
        //Обработчики админ сообщений в нужном порядке
    ];

    public static function handle(UserDTO &$userDTO, Message $update): bool
    {
        try
        {
            if (($update->message == "/admin" || $update->message == "вернуться в админку") && IsAdminSheets::handle($userDTO))
            {
                self::sendAdmin($userDTO);
                return true;
            }
            $status_send = false;
            foreach (self::$handlers as $handler)
            {
                if ($handler::handle($userDTO, $userDTO->update->getTypeMessage()))
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

    public static function sendAdmin(UserDTO &$userDTO): void
    {
        try
        {
            UserClearFields::handle($userDTO->user);
            $keyboard = new KeyboardReply(self::$adminKeyboard);
            $sendMessage = new SendMessage($userDTO->user);
            $sendMessage->reply_markup = $keyboard;
            $sendMessage->text =
                "Добро пожаловать в админ панель, чтобы вернуться в пользовательское меню, отправьте боту команду /start";
            $userDTO->bot->execute($sendMessage);
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
    }
}
