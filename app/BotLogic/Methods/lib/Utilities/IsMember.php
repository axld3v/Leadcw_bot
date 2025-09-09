<?php

namespace App\BotLogic\Methods\lib\Utilities;

use App\Models\User;
use App\Telegram\Methods\Info\GetChatMember;
use App\Telegram\TelegramBot;

class IsMember
{
    public static function handle(TelegramBot $bot, string|User $user, string $chat_id): bool
    {
        try
        {
            if (!is_string($user))
                $user = $user->user_id;
            $getChatMember = new GetChatMember();
            $getChatMember->chat_id = $chat_id;
            $getChatMember->user_id = $user;
            $info = $bot->execute($getChatMember);
            if ($info->isError) return false;
            $status = $info->result['status'] ?? "";

            if (empty($status) || $status == "left" || $status == "kicked")
                return false;
            else
                return true;
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return false;
    }
}
