<?php

namespace App\BotLogic\Methods\lib\SendMethods;

use App\DTO\UserDTO;
use App\Telegram\Methods\Message\SendMediaGroup;
use App\Telegram\Methods\Message\SendMessage;
use App\Telegram\TelegramBot;
use App\Telegram\Types\TelegramRequest\TelegramResult;
use App\Telegram\Types\TypeMessage\TypeMediaMessage;
use App\Telegram\Types\Update\Message;

class MediaMethods
{
    public static function add(Message $message, array $totalData): array|null
    {
        try
        {
            if (!empty($message->text_html))
                $totalData['text'] = $message->text_html;
            if ($message->isMedia)
            {
                if (isset($totalData['media']) && count($totalData['media']) >= 10)
                    return null; //Добавлено максимально возможное количество изображений
                if (isset($message->file['type']) && isset($message->file['file_id']))
                    $totalData['media'][] = $message->file;
            }
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return $totalData;
    }

    public static function send(
        UserDTO|null     $userDTO, array $totalData,
        TelegramBot|null $bot = null, string|null $user_id = null): TelegramResult|null
    {

        try
        {
            $tgBot = is_null($userDTO) ? $bot : $userDTO->bot;
            $user_id = is_null($userDTO) ? $user_id : $userDTO->user->user_id;
            $mediaMessage = new TypeMediaMessage($totalData);
            if (empty($mediaMessage->mediaItems))
            {
                $sendMessage = new SendMessage($user_id);
                $sendMessage->text = $totalData['text'];
                $rez = $tgBot->execute($sendMessage);
                return $rez;
            }

            $sendMediaGroup = new SendMediaGroup($user_id);
            $sendMediaGroup->media = $mediaMessage->mediaItems;
            $rez = $tgBot->execute($sendMediaGroup);
            return $rez;

        } catch
        (\Throwable $throwable)
        {
            logging($throwable);
        }

        return null;
    }
}
