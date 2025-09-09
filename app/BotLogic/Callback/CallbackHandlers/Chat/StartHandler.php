<?php

namespace App\BotLogic\Callback\CallbackHandlers\Chat;

use App\BotLogic\Callback\BaseCallbackHandler;
use App\BotLogic\Message\MessageHandlers\Chat\AddInfoHandler;
use App\BotLogic\Methods\lib\SendMethods\WhatChoose;
use App\BotLogic\Methods\UserMethods\UserClearFields;
use App\DTO\UserDTO;
use App\Telegram\Methods\Message\SendMessage;
use App\Telegram\Types\Keyboard\KeyboardReply;

class StartHandler extends BaseCallbackHandler
{
    public static function handle(UserDTO &$userDTO, \App\Telegram\Types\Update\Callback $update): bool
    {
        try
        {
            if ($update->button == "mainback")
            {
                WhatChoose::handle($userDTO, $update, '');
                return \App\BotLogic\Message\MessageHandlers\Chat\StartHandler::getStart($userDTO);
            }
            else if (str_contains($update->button, "geta_"))
            {
                $type = explode("_", $update->button)[1];
                if ($type == "date" && str_contains(mb_strtolower($update->text_button), "сегодн"))
                    $update->text_button = date('d.m.Y');

                $value = $update->text_button;
                if (str_contains(mb_strtolower($value), "другое")
                    || str_contains(mb_strtolower($value), "ввести")
                    || str_contains(mb_strtolower($value), "вручную"))
                {
                    WhatChoose::clear($userDTO, $update);
                    return true;
                }
                WhatChoose::handle($userDTO, $update, '');
                AddInfoHandler::addAnswerInfo($userDTO, $type, $value);
                AddInfoHandler::getNextQuestion($userDTO, $type);
                return true;
            }
            else if (str_contains($update->button, "add_info"))
            {
                WhatChoose::handle($userDTO, $update, '');

                self::publishInfo($userDTO);

                UserClearFields::handle($userDTO->user);
                $sendMessage = new SendMessage($userDTO->user);
                $sendMessage->text = "Данные успешно добавлены!";
                $sendMessage->reply_markup = new KeyboardReply([['Добавить информацию']]);
                $userDTO->execute($sendMessage);
                return true;
            }
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return false;
    }

    private static function publishInfo(UserDTO $userDTO)
    {
        try
        {
            $date = AddInfoHandler::getAnswerInfo($userDTO, 'date') ?? "";
            $requests_tg = AddInfoHandler::getAnswerInfo($userDTO, 'requests-tg') ?? "";
            $requests_inst = AddInfoHandler::getAnswerInfo($userDTO, 'requests-inst') ?? "";
            $requests_wat = AddInfoHandler::getAnswerInfo($userDTO, 'requests-wat') ?? "";
            $requests_all = AddInfoHandler::getAnswerInfo($userDTO, 'requests-all') ?? "";

            $name = ($userDTO->user->first_name ?? "") . " " . ($userDTO->user->last_name ?? "");
            $userDTO->getSheets()->appendLastRow([[
                $date, $name, $userDTO->user->username, $requests_tg, $requests_inst,
                $requests_wat, $requests_all
            ]], 'Данные', 'A2:G');

        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
    }
}
