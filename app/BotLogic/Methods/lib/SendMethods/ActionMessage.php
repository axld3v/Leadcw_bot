<?php

namespace App\BotLogic\Methods\lib\SendMethods;

use App\Models\User;
use App\Telegram\Methods\Message\SendMessage;
use App\Telegram\Methods\Message\SendPhoto;
use App\Text\getText;

class ActionMessage
{
    public static function handle(string $nameAction, User $user): SendMessage|SendPhoto|null
    {
        try
        {
            $action = getText::getBySheets($nameAction);
            if (is_null($action)) return null;

            if (!empty($action['image']))
            {
                $sendPhoto = new SendPhoto($user);
                $sendPhoto->caption = $action['text'];
                $sendPhoto->photo = $action['image'];
                return $sendPhoto;
            }
            if (!empty($action['text']))
            {
                $sendMessage = new SendMessage($user);
                $sendMessage->text = $action['text'];
                return $sendMessage;
            }
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return null;
    }
}
