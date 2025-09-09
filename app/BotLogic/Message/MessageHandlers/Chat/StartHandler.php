<?php

namespace App\BotLogic\Message\MessageHandlers\Chat;

use App\BotLogic\Message\BaseMessageHandler;
use App\BotLogic\Methods\lib\AdminMethods\IsAdminSheets;
use App\BotLogic\Methods\UserMethods\UserClearFields;
use App\DTO\UserDTO;
use App\Telegram\Methods\Message\SendMessage;
use App\Telegram\Types\Keyboard\KeyboardRemoveReply;
use App\Telegram\Types\Keyboard\KeyboardReply;
use App\Telegram\Types\Update\Message;
use App\Text\getText;

class StartHandler extends BaseMessageHandler
{
    public static function handle(UserDTO &$userDTO, Message $update): bool
    {
        try
        {
            if ($update->message == "/start" || $update->message == "вернуться в меню")
            {
                return self::getStart($userDTO);
            }
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return false;
    }

    public static function getStart(UserDTO &$userDTO): bool
    {
        UserClearFields::handle($userDTO->user);
        $admin = IsAdminSheets::handle($userDTO);
        if ($admin)
        {
            $sendMessage = new SendMessage($userDTO->user);
            $sendMessage->text = getText::getBySheets('start');
            $sendMessage->reply_markup = new KeyboardReply([['Добавить информацию']]);
            $userDTO->execute($sendMessage);
        }
        else
        {
            $sendMessage = new SendMessage($userDTO->user);
            $sendMessage->text = getText::getBySheets('start');
            $sendMessage->reply_markup = new KeyboardRemoveReply();
            $userDTO->execute($sendMessage);
        }
        return true;
    }

}
