<?php

namespace App\BotLogic\Message\MessageHandlers\Group;

use App\BotLogic\Message\BaseMessageHandler;
use App\DTO\UserDTO;
use App\Models\DictionaryVariable;
use App\Telegram\Methods\Message\DeleteMessage;
use App\Telegram\Methods\Message\SendMessage;
use App\Telegram\Types\Update\Message;

class GroupHandler extends BaseMessageHandler
{
    public static function handle(UserDTO &$userDTO, Message $update): bool
    {
        try
        {
            if (empty($update->message) && empty($update->file)) return true;
            if ($update->message == "/set0id")//Привязка группы
            {
                $sendMessage = new SendMessage($update->chat_id);
                $sendMessage->text = "Группа успешно подключена!";
                $userDTO->bot->execute($sendMessage);

                $deleteMessage = new DeleteMessage($update->chat_id);
                $deleteMessage->message_id = $update->message_id;
                $userDTO->bot->execute($deleteMessage);
                DictionaryVariable::query()->updateOrInsert(
                    ['title' => 'admin_chat'],
                    [
                        'info_json' => json_encode(
                            ['group_id' => $update->chat_id], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    ]
                );
                return true;
            }
            return true; //Заблокировать использование бота в группах
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return true;
    }
}
