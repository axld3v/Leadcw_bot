<?php

namespace App\BotLogic\Methods\lib\Utilities;

use App\DTO\UserDTO;
use App\Telegram\Methods\Message\SendMessage;
use App\Telegram\Types\Keyboard\KeyboardReply;

class ParseButtonByText
{
    public static function sendGetMessage(UserDTO $userDTO, string $chat_id = "", array $buttons = []): void
    {
        try
        {
            if (empty($chat_id)) $chat_id = $userDTO->user->user_id;
            if (empty($buttons)) $buttons = [['Пропустить'], ['Вернуться в админку']];

            $sendMessage = new SendMessage();
            $sendMessage->chat_id = $chat_id;
            $sendMessage->text = "Отправьте мне список кнопок в одном сообщении. Если пост должен быть без кнопок - нажмите Пропустить
<pre>Кнопка с ссылкой - https://google.com</pre>

Используйте разделитель |, чтобы сделать кнопки в один ряд. Пример:
<pre>Кнопка 1 - https://yandex.ru | Кнопка 2 - https://google.com
Кнопка 3 - https://google.com | Кнопка 4 - https://google.com</pre>";
            $sendMessage->reply_markup = new KeyboardReply($buttons);
            $userDTO->execute($sendMessage);
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
    }

    public static function getButtons(string $msg): array
    {
        $buttons = [];
        try
        {
            $lines = explode("\n", $msg);
            $lines = array_diff($lines, [], [null]);

            foreach ($lines as $line)
            {
                $line_arr = explode("|", trim($line));
                $line_arr = array_diff($line_arr, [], [null]);

                $buttons_line = [];

                foreach ($line_arr as $line_item)
                {
                    try
                    {
                        $line_item = trim($line_item);
                        if (!str_contains($line_item, "-")) continue;
                        $spl = explode("-", $line_item);
                        if (count($spl) < 2) continue;

                        $title = $spl[0];
                        unset($spl[0]);
                        $spl = array_values($spl);
                        $link = implode("-", $spl);
                        $buttons_line[] = ['text' => trim($title), 'url' => trim($link)];
                    } catch (\Throwable $throwable)
                    {
                        logging($throwable);
                    }
                }
                if (!empty($buttons_line)) $buttons[] = $buttons_line;
            }
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return $buttons;
    }
}
