<?php

namespace App\BotLogic\Message\MessageHandlers\Chat;

use App\BotLogic\Message\BaseMessageHandler;
use App\DTO\UserDTO;
use App\Telegram\Methods\Message\SendMessage;
use App\Telegram\Types\Keyboard\KeyboardInline;
use App\Telegram\Types\Keyboard\KeyboardReply;
use App\Telegram\Types\Update\Message;
use App\Text\getText;

class AddInfoHandler extends BaseMessageHandler
{
    public static array $actions = [
        ['action' => 'date', 'type' => 'btn'],
        ['action' => 'requests-tg', 'type' => 'btn'],
        ['action' => 'requests-inst', 'type' => 'btn'],
        ['action' => 'requests-wat', 'type' => 'btn'],
        ['action' => 'requests-all', 'type' => 'btn'],
    ];

    public static function handle(UserDTO &$userDTO, Message $update): bool
    {
        try
        {
            if ($update->message == "добавить информацию")
            {
                self::getByActionBtn($userDTO, "date");
                return true;
            }
            else if (str_contains($userDTO->user->lastmessage, "geta_"))
            {
                $type = explode("_", $userDTO->user->lastmessage)[1];
                if ($update->message == "назад")
                {
                    self::getPreviousQuestion($userDTO, $type);
                    return true;
                }
                if (str_contains(mb_strtolower($update->msg), "пропустить"))
                    $update->msg = "";

                self::addAnswerInfo($userDTO, $type, $update->msg);
                self::getNextQuestion($userDTO, $type);
                return true;
            }
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return false;
    }

    private static function getByActionBtn(UserDTO $userDTO, string $action): void
    {
        try
        {
            $count = 2;
            $items = self::getItemsBtn(getText::getBySheets($action)['items'], $action);
            if ($items >= 10) $count = 5;
            $sendMessage = new SendMessage($userDTO->user);
            $sendMessage->text = getText::getBySheets($action)['text'];
            $sendMessage->reply_markup = new KeyboardInline(
                $items, $count
            );
            $userDTO->user->updateAndSave(['lastmessage' => "geta_{$action}_info"]);
            $userDTO->execute($sendMessage);
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
    }

    private static function getByActionNoBtn(UserDTO $userDTO, string $action): void
    {
        try
        {
            $sendMessage = new SendMessage($userDTO->user);
            $sendMessage->text = getText::getBySheets($action)['text'];
            $sendMessage->reply_markup = new KeyboardReply([
                ['Пропустить'],
                ['Назад', 'Вернуться в меню']
            ]);
            $userDTO->user->updateAndSave(['lastmessage' => "geta_{$action}_info"]);
            $userDTO->execute($sendMessage);
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
    }

    private static function getItemsBtn(array $items, string $key): array
    {
        $buttons = [];
        try
        {
            foreach ($items as $i => $item)
            {
                $buttons[] = [$item, "geta_{$key}_$i"];
            }
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return $buttons;
    }

    public static function addAnswerInfo(UserDTO $userDTO, string $key, string $value): void
    {
        try
        {
            $totalValues = $userDTO->user->getJsonInfo('info_values') ?? [];
            if (!is_array($totalValues)) $totalValues = json_decode($totalValues, true);
            $totalValues[$key] = $value;
            $userDTO->user->setJsonInfo('info_values', json_encode($totalValues));
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
    }

    public static function getAnswerInfo(UserDTO $userDTO, string $key): mixed
    {
        try
        {
            $totalValues = $userDTO->user->getJsonInfo('info_values') ?? [];
            if (!is_array($totalValues)) $totalValues = json_decode($totalValues, true);
            return $totalValues[$key] ?? "";
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return "";
    }

    public static function getNextQuestion(UserDTO $userDTO, string $type): void
    {
        $nextType = [];
        for ($i = 0; $i < count(self::$actions); $i++)
        {
            if (self::$actions[$i]['action'] != $type) continue;
            if (isset(self::$actions[$i + 1]['action']))
            {
                if (self::$actions[$i]['action'] == 'prepayment')
                {

                }
                $nextType = self::$actions[$i + 1];
            }
        }
        if (empty($nextType))
        {
            self::successSend($userDTO);
            return;
        }
        if ($nextType['type'] == 'btn')
            self::getByActionBtn($userDTO, $nextType['action']);
        else
            self::getByActionNoBtn($userDTO, $nextType['action']);

    }


    public static function getPreviousQuestion(UserDTO $userDTO, string $type): void
    {
        $previousType = [];
        for ($i = 0; $i < count(self::$actions); $i++)
        {
            if (self::$actions[$i]['action'] != $type) continue;
            if (isset(self::$actions[$i - 1]['action']))
            {
                if (self::$actions[$i - 1]['action'] == "prepaymentget")
                {

                }
                $previousType = self::$actions[$i - 1];

            }
        }
        if (empty($previousType))
        {
            StartHandler::getStart($userDTO);
            return;
        }
        self::addAnswerInfo($userDTO, $type, "");
        if ($previousType['type'] == 'btn')
            self::getByActionBtn($userDTO, $previousType['action']);
        else
            self::getByActionNoBtn($userDTO, $previousType['action']);

    }

    private static function successSend(UserDTO $userDTO): void
    {
        try
        {
            $date = self::getAnswerInfo($userDTO, 'date') ?? "";
            $requests_tg = self::getAnswerInfo($userDTO, 'requests-tg') ?? "";
            $requests_inst = self::getAnswerInfo($userDTO, 'requests-inst') ?? "";
            $requests_wat = self::getAnswerInfo($userDTO, 'requests-wat') ?? "";
            $requests_all = self::getAnswerInfo($userDTO, 'requests-all') ?? "";

            $text = "Проверьте данные\n\n" .
                "<b>Дата: </b>$date\n" .
                "<b>Заявки телеграм : </b>$requests_tg\n" .
                "<b>Заявки инстаграм: </b>$requests_inst\n" .
                "<b>Заявки вотсап: </b>$requests_wat\n" .
                "<b>Кол-во продаж: </b>$requests_all\n";

            $sendMessage = new SendMessage($userDTO->user);
            $sendMessage->text = $text;
            $sendMessage->reply_markup = new KeyboardInline([
                ['✅ Добавить', 'add_info'],
                ['❌ Отменить', 'mainback']
            ], 2);
            $userDTO->execute($sendMessage);
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
    }

    public static function extractNumber($string): string
    {
        // Ищем число с точкой, запятой или без разделителя
        if (preg_match('/-?\d+([.,]\d+)?/', $string, $matches))
        {
            // Заменяем запятую на точку для корректного преобразования в float
            return $matches[0] ?? "";
        }
        return "";
    }

    public static function toDouble(string $str): float
    {
        $str = str_replace(",", ".", $str);
        $str = str_replace(" ", "", $str);
        return doubleval($str);
    }

}
