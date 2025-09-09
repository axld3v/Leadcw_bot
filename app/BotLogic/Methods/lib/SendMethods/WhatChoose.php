<?php

namespace App\BotLogic\Methods\lib\SendMethods;

use App\DTO\UserDTO;
use App\Telegram\Methods\Message\EditMessageCaption;
use App\Telegram\Methods\Message\EditMessageText;
use App\Telegram\Types\TelegramRequest\TelegramResult;
use App\Telegram\Types\Update\Callback;

class WhatChoose
{
    public static function handle(UserDTO $userDTO, Callback $update, string $title = "✅ ", string $chat_id = ""): TelegramResult|null
    {
        if (empty($chat_id)) $chat_id = $userDTO->user->user_id;
        $text = $update->message->text_html . "\n\n" . $title . "<b>" . $update->text_button . "</b>";
        if ($update->message->isMedia)
        {
            $actionEdit = new EditMessageCaption();
            $actionEdit->caption = $text;
        }
        else
        {
            $actionEdit = new EditMessageText();
            $actionEdit->text = $text;
        }
        $actionEdit->message_id = $update->message_id;
        $actionEdit->chat_id = $chat_id;
        $info = $userDTO->execute($actionEdit);
        return $info;
    }

    public static function clear(UserDTO $userDTO, Callback $update, string $chat_id = ""): TelegramResult|null
    {
        if (empty($chat_id)) $chat_id = $userDTO->user->user_id;
        $text = $update->message->text_html."\n\n<b>Отправьте свой вариант</b>";
        if ($update->message->isMedia)
        {
            $actionEdit = new EditMessageCaption();
            $actionEdit->caption = $text;
        }
        else
        {
            $actionEdit = new EditMessageText();
            $actionEdit->text = $text;
        }
        $actionEdit->message_id = $update->message_id;
        $actionEdit->chat_id = $chat_id;
        $info = $userDTO->execute($actionEdit);
        return $info;
    }
}
