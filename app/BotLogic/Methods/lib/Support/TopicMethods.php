<?php

namespace App\BotLogic\Methods\lib\Support;

use App\DTO\UserDTO;
use App\Models\DictionaryVariable;
use App\Models\User;
use App\Telegram\Methods\Message\CopyMessage;
use App\Telegram\Methods\Message\SendMessage;
use App\Telegram\Methods\Topic\CreateForumTopic;
use App\Telegram\Methods\Topic\ReopenForumTopic;
use App\Telegram\TelegramBot;
use App\Telegram\Types\Keyboard\KeyboardInline;
use App\Telegram\Types\Keyboard\KeyboardReply;
use App\Telegram\Types\TelegramRequest\TelegramResult;
use App\Telegram\Types\Update\Message;

class TopicMethods
{
    static string|null $groupId = null;

    public static function copyMessageToTopic(UserDTO             &$userDTO, bool $addLinkUserBtn = false,
                                              KeyboardInline|null $keyboard = null, bool $repeat = false): TelegramResult|null
    {
        try
        {
            self::checkCreatedTopicUser($userDTO);
            $copyMessage = new CopyMessage();
            $copyMessage->message_thread_id = $userDTO->user->support_chat;
            $copyMessage->chat_id = self::getAdminGroupId();
            $copyMessage->from_chat_id = $userDTO->user->user_id;
            $copyMessage->message_id = $userDTO->update->getTypeMessage()->message_id;
            $copyMessage_NoUserBtn = clone $copyMessage;

            $addKeyboard = [];
            if ($addLinkUserBtn) $addKeyboard =
                new KeyboardInline([['text' => '👤 Открыть профиль', 'url' => 'tg://user?id=' . $userDTO->user->user_id]]);

            if (!empty($keyboard) && !empty($addKeyboard))
            {
                $addKeyboard = KeyboardInline::merge($keyboard, $addKeyboard);
                $copyMessage->reply_markup = $addKeyboard;
                $copyMessage_NoUserBtn->reply_markup = $keyboard;
            }
            else if (empty($keyboard) && !empty($addKeyboard))
            {
                $copyMessage->reply_markup = $addKeyboard;
            }
            else if (!empty($keyboard) && empty($addKeyboard))
            {
                $copyMessage->reply_markup = $keyboard;
                $copyMessage_NoUserBtn = null;
            }
            $info = $userDTO->bot->execute($copyMessage, $copyMessage_NoUserBtn);
            if ($info->isError && $info->error = "Bad Request: message thread not found" && !$repeat)
            {
                $new = false;
                try
                {
                    $reopen = new ReopenForumTopic();
                    $reopen->chat_id = self::getAdminGroupId();
                    $reopen->message_thread_id = $userDTO->user->support_chat;
                    $rez = $userDTO->bot->execute($reopen);
                    if (isset($rez->error) && $rez->error == "Bad Request: TOPIC_ID_INVALID")
                        $new = true;
                } catch (\Throwable $throwable)
                {

                }
                if ($new)
                {
                    $userDTO->user->updateAndSave(['support_chat' => null]);
                    return self::copyMessageToTopic($userDTO, addLinkUserBtn: $addLinkUserBtn, keyboard: $keyboard, repeat: true);
                }
            }
            return $info;
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return null;
    }

    private static function checkCreatedTopicUser(UserDTO $userDTO): int|null
    {
        if (!empty($userDTO->user->support_chat))
            return $userDTO->user->support_chat;
        try
        {
            $topic_id =
                TopicMethods::createTopic($userDTO->bot, self::getAdminGroupId(),
                    $userDTO->user->first_name . " " . $userDTO->user->last_name);
            if ($topic_id == 0) return null;

            $name = ($userDTO->user->first_name ?? "") . " " . ($userDTO->user->last_name ?? "");

            $text = "Пользователь $name\n" .
                "Username: @{$userDTO->user->username}\n" .
                "Дата регистрации {$userDTO->user->created_at->format("d.m.Y H:i")}";
            $userDTO->user->updateAndSave(['support_chat' => $topic_id]);
            self::sendTextMessageToTopic($userDTO, $text, addLinkUserBtn: true, isFirst: true);
            usleep(30000);
            return $topic_id;
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return 0;
    }

    public static function createTopic(TelegramBot $bot, string $chat_id, string $nameTopic): int
    {
        try
        {
            $createForumTopic = new CreateForumTopic($chat_id);
            $createForumTopic->name = $nameTopic;

            $rez = $bot->execute($createForumTopic);
            if (!$rez->isError)
                return $rez->result["message_thread_id"] ?? 0;
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return 0;
    }

    public static function getAdminGroupId(): string|null
    {
        if (is_null(self::$groupId))
        {
            $qroup_query = DictionaryVariable::query()->where('title', 'admin_chat')->get();
            self::$groupId = isset($qroup_query->first()->info_json['group_id'])
                ? $qroup_query->first()->info_json['group_id']
                : null;
        }
        return self::$groupId;
    }

    public static function sendTextMessageToTopic(UserDTO             &$userDTO,
                                                  string              $text,
                                                  bool                $addLinkUserBtn = false,
                                                  bool                $isFirst = false,
                                                  KeyboardInline|null $keyboard = null,
                                                  bool                $repeat = false): TelegramResult|null
    {
        self::checkCreatedTopicUser($userDTO);
        $sendMessage = new SendMessage();
        $sendMessage->message_thread_id = $userDTO->user->support_chat;
        $sendMessage->chat_id = self::getAdminGroupId();
        $sendMessage->text = $text;
        $sendMessage_NoUserBtn = clone $sendMessage;

        $addKeyboard = [];
        if ($addLinkUserBtn)
        {
            $addKeyboard[] = ['text' => '👤 Открыть профиль', 'url' => 'tg://user?id=' . $userDTO->user->user_id];
            $addKeyboard = new KeyboardInline($addKeyboard, 1);
        }

        if (!empty($keyboard) && !empty($addKeyboard))
        {
            $addKeyboard = KeyboardInline::merge($keyboard, $addKeyboard);
            $sendMessage->reply_markup = $addKeyboard;
            $sendMessage_NoUserBtn->reply_markup = $keyboard;
        }
        else if (empty($keyboard) && !empty($addKeyboard))
        {
            $sendMessage->reply_markup = $addKeyboard;
        }
        else if (!empty($keyboard) && empty($addKeyboard))
        {
            $sendMessage->reply_markup = $keyboard;
            $sendMessage_NoUserBtn = null;
        }
        $info = $userDTO->bot->execute($sendMessage, $sendMessage_NoUserBtn);
        if ($info->isError && $info->error = "Bad Request: message thread not found" && !$repeat)
        {
            $new = false;
            try
            {
                $reopen = new ReopenForumTopic();
                $reopen->chat_id = self::getAdminGroupId();
                $reopen->message_thread_id = $userDTO->user->support_chat;
                $rez = $userDTO->bot->execute($reopen);
                if (isset($rez->error) && $rez->error == "Bad Request: TOPIC_ID_INVALID")
                    $new = true;
            } catch (\Throwable $throwable)
            {

            }
            if ($new)
            {
                $userDTO->user->updateAndSave(['support_chat' => null]);
                return self::sendTextMessageToTopic($userDTO, $text, addLinkUserBtn: $addLinkUserBtn, isFirst: $isFirst, keyboard: $keyboard, repeat: true);
            }
        }
        return $info;
    }

    public static function copyMessageToUser(TelegramBot $bot, User $user, Message $update, bool $backKeyboard = false): TelegramResult|null
    {
        try
        {
            $user->updateAndSave(['lastmessage' => 'chat_support']);
            $copyMessage = new CopyMessage($user);
            $copyMessage->from_chat_id = self::getAdminGroupId();
            $copyMessage->message_id = $update->message_id;

            $addKeyboard = [];
            if ($backKeyboard)
            {
                $addKeyboard =
                    new KeyboardReply(["Завершить диалог"]);
            }
            if (!empty($addKeyboard))
            {
                $copyMessage->reply_markup = $addKeyboard;
            }

            return $bot->execute($copyMessage);
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return null;
    }
}
