<?php

namespace App\BotLogic\Methods\lib\Utilities;

use App\DTO\UserDTO;
use App\Telegram\Methods\Message\GetFile;
use App\Telegram\TelegramBot;
use App\Telegram\Types\Update\Message;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DownloadFile
{
    public static function getFilePath(Message $update, UserDTO|null $userDTO = null, TelegramBot|null $bot = null): string|null
    {
        try
        {
            if (empty($bot)) $bot = $userDTO->bot;
            $getFilePath = new GetFile();
            $getFilePath->file_id = $update->file['file_id'];
            $response = $bot->execute($getFilePath);
            if (!isset($response->result['file_path'])) return null;
            return $response->result['file_path'];
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return null;
    }

    public static function downloadPhoto(TelegramBot $bot, string $telegram_path): string|null
    {
        $link = str_replace("https://api.telegram.org", "https://api.telegram.org/file/", $bot->getUrlBot())
            . "/" . $telegram_path;
        try
        {
            // Получение содержимого изображения
            $response = Http::get($link);

            if (!$response->successful()) return null;


            $ext = explode(".", $telegram_path);
            // Генерация случайного имени файла с расширением .png
            $filename = uniqid() . '.' . end($ext);
            // Сохранение изображения в папку storage/images
            $path = 'images/' . $filename;
            $rez = Storage::disk('local')->put($path, $response->body());
            $url = storage_path('app/' . $path);
            return $url;

        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return null;
    }

    public static function downloadFile(TelegramBot $bot, string $telegram_path, string $path_to_save): string|null
    {
        $link = str_replace("https://api.telegram.org", "https://api.telegram.org/file/", $bot->getUrlBot())
            . "/" . $telegram_path;
        try
        {
            // Получение содержимого изображения
            $response = Http::get($link);

            if (!$response->successful()) return null;

            $ext = explode(".", $telegram_path);
            $fileType = end($ext);
            $filename = uniqid() . '.' . $fileType;
            $path = $path_to_save;
            if (!isset(pathinfo($path_to_save)['extension']))
            {
                if (mb_substr($path_to_save, mb_strlen($path_to_save) - 1, 1) == "/")
                    $path = mb_substr($path_to_save, 0, mb_strlen($path_to_save) - 1);

                if (mb_substr($path_to_save, mb_strlen($path_to_save) - 1, 1) != "\\")
                    $path .= "\\";

                $path .= $filename;
            }
            $rez = file_put_contents($path, $response->body());
            return $path;

        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return null;
    }
}
