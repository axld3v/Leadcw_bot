<?php

namespace App\BotLogic\Methods\lib\SendMethods;

use App\BotLogic\Methods\lib\Support\TopicMethods;
use App\DTO\UserDTO;
use App\Telegram\Methods\Message\CopyMessage;
use App\Telegram\Methods\Message\SendMediaGroup;
use App\Telegram\TelegramBot;
use App\Telegram\Types\TypeMessage\TypeMediaMessage;
use App\Telegram\Types\Update\Message;

class CombineMethods
{
    public static function add(UserDTO|null $userDTO, Message $update, array $totalFiles = []): array|null
    {
        try
        {
            if (empty($totalFiles) && !is_null($userDTO) && !empty($userDTO->user->getJsonInfo('files')))
            {
                $totalFiles = $userDTO->user->getJsonInfo('files');
                if (!is_array($totalFiles)) $totalFiles = json_decode($totalFiles, true);
            }

            $type = self::getType($update);
            if ($type != "copy" && !empty($update->file))
                $totalFiles[$type][] = $update->file;
            else
                $totalFiles['copy'][] = [$update->chat_id, $update->message_id];
            if (is_null($userDTO)) return $totalFiles;
            $userDTO->user->setJsonInfo('files', json_encode($totalFiles));
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return null;
    }

    public static function send(UserDTO|null $userDTO, TelegramBot $bot, array $totalFiles = [], string $chat_id = ""): array|null
    {
        try
        {
            $totalFiles = [];
            if (!is_null($userDTO))
            {
                $chat_id = $userDTO->user->user_id;
                $totalFiles = $userDTO->user->getJsonInfo('files');
                if (!is_array($totalFiles)) $totalFiles = json_decode($totalFiles, true);
            }
            if (!empty($totalFiles['media']))
            {
                $medias = array_chunk($totalFiles['media'], 10);
                foreach ($medias as $media_row)
                {
                    try
                    {
                        if (empty($media_row)) continue;
                        $media = new TypeMediaMessage(['media' => $media_row]);
                        $sendMedia = new SendMediaGroup();
                        $sendMedia->media = $media->mediaItems;
                        $sendMedia->chat_id = TopicMethods::getAdminGroupId();
                        if (is_null($userDTO)) $bot->execute($sendMedia);
                        else $userDTO->execute($sendMedia);
                    } catch (\Throwable  $throwable)
                    {
                        logging($throwable);
                    }
                }
            }

            if (!empty($totalFiles['document']))
            {
                $medias = array_chunk($totalFiles['document'], 10);
                foreach ($medias as $media_row)
                {
                    try
                    {
                        if (empty($media_row)) continue;
                        $media = new TypeMediaMessage(['media' => $media_row]);
                        $sendMedia = new SendMediaGroup();
                        $sendMedia->media = $media->mediaItems;
                        $sendMedia->chat_id = TopicMethods::getAdminGroupId();
                        if (is_null($userDTO)) $bot->execute($sendMedia);
                        else $userDTO->execute($sendMedia);
                    } catch (\Throwable  $throwable)
                    {
                        logging($throwable);
                    }
                }
            }

            if (!empty($totalFiles['copy']))
            {
                foreach ($totalFiles['copy'] as $media_item)
                {
                    try
                    {
                        if (empty($media_item)) continue;
                        $copyMessage = new CopyMessage();
                        $copyMessage->chat_id = TopicMethods::getAdminGroupId();
                        $copyMessage->from_chat_id = $media_item[0];
                        $copyMessage->message_id = $media_item[1];
                        if (is_null($userDTO)) $bot->execute($copyMessage);
                        else $userDTO->execute($copyMessage);
                    } catch (\Throwable  $throwable)
                    {
                        logging($throwable);
                    }
                }
            }
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return null;
    }

    public static function getType(Message $update): string
    {
        if ($update->isMedia)
        {
            if (!empty($update->file['type']))
            {
                $type = $update->file['type'];
                if ($type == "photo" || $type == "video" || $type == "animation")
                {
                    return "media";
                }
                else if ($type == "document")
                    return $type;
            }
        }
        return "copy";
    }
}
