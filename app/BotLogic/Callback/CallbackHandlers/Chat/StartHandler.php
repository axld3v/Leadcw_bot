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
                $value = $update->text_button;
                if (str_contains(mb_strtolower($value), "другое"))
                {
                    WhatChoose::clear($userDTO, $update);
                    return true;
                }
                WhatChoose::handle($userDTO, $update, '');
                $type = explode("_", $update->button)[1];
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
            $who = AddInfoHandler::getAnswerInfo($userDTO, 'who') ?? "";
            $name = AddInfoHandler::getAnswerInfo($userDTO, 'name') ?? "";
            $phone = AddInfoHandler::getAnswerInfo($userDTO, 'phone') ?? "";
            $typecontact = AddInfoHandler::getAnswerInfo($userDTO, 'typecontact') ?? "";
            $source = AddInfoHandler::getAnswerInfo($userDTO, 'source') ?? "";
            $brand = AddInfoHandler::getAnswerInfo($userDTO, 'brand') ?? "";
            $complexion = AddInfoHandler::getAnswerInfo($userDTO, 'complexion') ?? "";
            $agent = AddInfoHandler::getAnswerInfo($userDTO, 'agent') ?? "";
            $price = AddInfoHandler::getAnswerInfo($userDTO, 'price') ?? "";
            $zahod = AddInfoHandler::getAnswerInfo($userDTO, 'zahod') ?? "";
            $prepayment = AddInfoHandler::getAnswerInfo($userDTO, 'prepayment') ?? "";
            $prepaymentget = AddInfoHandler::getAnswerInfo($userDTO, 'prepaymentget') ?? "";
            $city = AddInfoHandler::getAnswerInfo($userDTO, 'city') ?? "";
            $type = AddInfoHandler::getAnswerInfo($userDTO, 'type') ?? "";
            $additionalcomment = AddInfoHandler::getAnswerInfo($userDTO, 'additionalcomment') ?? "";
            $ostatok = "";

            $priceVal = AddInfoHandler::extractNumber($price);
            $zahodVal = AddInfoHandler::extractNumber($zahod);
            $rateVal = str_replace($zahodVal, "", $zahod);
            $pribil = AddInfoHandler::toDouble($priceVal) - AddInfoHandler::toDouble($zahodVal);
            $pribil = strval($pribil) . $rateVal;

            if ($prepayment == "Да")
            {
                $prepaymentVal = AddInfoHandler::extractNumber($prepaymentget);
                $rate = str_replace($priceVal, "", $price);

                $ostatok = AddInfoHandler::toDouble($priceVal) - AddInfoHandler::toDouble($prepaymentVal);
                $ostatok = strval($ostatok) . $rate;

            }
            else
            {
                $prepaymentget = "";
                $ostatok = "";
            }

            $userDTO->getSheets()->appendLastRow([[
                date('d.m.Y H:i'), $who, $name, $phone, $typecontact,
                $source, $brand, $complexion, $agent, $price, $zahod, $pribil,
                $prepaymentget, $ostatok, $city, $type, $additionalcomment
            ]], 'Данные', 'A2:O');

        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
    }
}
