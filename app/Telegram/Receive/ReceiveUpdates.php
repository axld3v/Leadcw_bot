<?php

namespace App\Telegram\Receive;

use App\BotLogic\EventRouterAction;
use App\DTO\UserDTO;
use App\Models\User;
use App\Telegram\Methods\Webhook\GetUpdates;
use App\Telegram\TelegramBot;
use App\Telegram\Types\Update\Callback;
use App\Telegram\Types\Update\Message;
use App\Telegram\Types\Update\Query;

class ReceiveUpdates
{
    public static function receive(array $json = []): void
    {
        try
        {
            $telegramBot = new TelegramBot();
            if (empty($json))
            {
                $getUpdates = new GetUpdates();
                $getUpdates->offset = 473019966;

                $info = $telegramBot->execute($getUpdates);

                if (count($info->result) >= 99)
                {
                    echo "ПЕРЕПОЛНЕНИЕ GetUpdates, установите offset на " . end($info->result)['update_id'];
                    return;
                }
                $lastUpdate = end($info->result);
                if (empty($lastUpdate))
                {
                    echo "No message";
                    return;
                }

                $type = array_key_last($lastUpdate);
                $itemUpdate = $lastUpdate[$type];
            }
            else
            {
                $lastUpdate = $json;
                $type = array_key_last($lastUpdate);
                $itemUpdate = $lastUpdate[$type];
            }
            echo "update_id: " . $lastUpdate['update_id'] . "\n";

            if ($type == "message" || $type == "channel_post")
            {
                $message = new Message($itemUpdate);
                if ($message->isBotMessage) return;
                $user = User::registration($itemUpdate['from']);
                if (is_null($user)) return;
                $userDTO = new UserDTO($user, $telegramBot, $message);
                EventRouterAction::handle($userDTO);
            }
            else if ($type == "callback_query")
            {
                $callback = new Callback($itemUpdate);
                $user = User::registration($itemUpdate['from']);
                if (is_null($user)) return;
                $userDTO = new UserDTO($user, $telegramBot, $callback);
                EventRouterAction::handle($userDTO);
            }
            else if ($type == "inline_query")
            {
                $query = new Query($itemUpdate);
                $user = User::registration($itemUpdate['from']);
                if (is_null($user)) return;
                $userDTO = new UserDTO($user, $telegramBot, $query);
                EventRouterAction::handle($userDTO);
            }
            else if ($type == "edited_message")
            {
                //receive edited message
            }
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
    }
}
