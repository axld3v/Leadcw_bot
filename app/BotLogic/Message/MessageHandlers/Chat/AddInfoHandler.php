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
        ['action' => 'who', 'type' => 'btn'],
        ['action' => 'name', 'type' => 'nobtn'],
        ['action' => 'phone', 'type' => 'nobtn'],
        ['action' => 'typecontact', 'type' => 'btn'],
        ['action' => 'source', 'type' => 'btn'],
        ['action' => 'brand', 'type' => 'btn'],
        ['action' => 'complexion', 'type' => 'nobtn'],
        ['action' => 'agent', 'type' => 'btn'],
        ['action' => 'price', 'type' => 'nobtn'],
        ['action' => 'zahod', 'type' => 'nobtn'],
        ['action' => 'prepayment', 'type' => 'btn'],
        ['action' => 'prepaymentget', 'type' => 'nobtn'],
        ['action' => 'city', 'type' => 'btn'],
        ['action' => 'type', 'type' => 'btn'],
        ['action' => 'additionalcomment', 'type' => 'nobtn'],
    ];

    public static function handle(UserDTO &$userDTO, Message $update): bool
    {
        try
        {
            if ($update->message == "добавить информацию")
            {
                self::getByActionBtn($userDTO, "who");
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
            $sendMessage = new SendMessage($userDTO->user);
            $sendMessage->text = getText::getBySheets($action)['text'];
            $sendMessage->reply_markup = new KeyboardInline(
                self::getItemsBtn(getText::getBySheets($action)['items'], $action), 2
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
                    $answer = self::getAnswerInfo($userDTO, 'prepayment');
                    if ($answer == "Нет")
                    {
                        $nextType = self::$actions[$i + 2];
                        continue;
                    }
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
                    if (self::getAnswerInfo($userDTO, 'prepayment') == "Нет")
                    {
                        $previousType = self::$actions[$i - 2];
                        continue;
                    }
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
            $who = self::getAnswerInfo($userDTO, 'who') ?? "";
            $name = self::getAnswerInfo($userDTO, 'name') ?? "";
            $phone = self::getAnswerInfo($userDTO, 'phone') ?? "";
            $typecontact = self::getAnswerInfo($userDTO, 'typecontact') ?? "";
            $source = self::getAnswerInfo($userDTO, 'source') ?? "";
            $brand = self::getAnswerInfo($userDTO, 'brand') ?? "";
            $complexion = self::getAnswerInfo($userDTO, 'complexion') ?? "";
            $agent = self::getAnswerInfo($userDTO, 'agent') ?? "";
            $price = self::getAnswerInfo($userDTO, 'price') ?? "";
            $zahod = AddInfoHandler::getAnswerInfo($userDTO, 'zahod') ?? "";
            $prepayment = self::getAnswerInfo($userDTO, 'prepayment') ?? "";
            $prepaymentget = self::getAnswerInfo($userDTO, 'prepaymentget') ?? "";
            $city = self::getAnswerInfo($userDTO, 'city') ?? "";
            $type = self::getAnswerInfo($userDTO, 'type') ?? "";
            $additionalcomment = self::getAnswerInfo($userDTO, 'additionalcomment') ?? "";

            $priceVal = self::extractNumber($price);
            $zahodVal = AddInfoHandler::extractNumber($zahod);
            $rateVal = str_replace($zahodVal, "", $zahod);
            $pribil = AddInfoHandler::toDouble($priceVal) - AddInfoHandler::toDouble($zahodVal);
            $pribil = strval($pribil) . $rateVal;

            $ostatok = "";
            if ($prepayment == "Да")
            {
                $priceVal = self::extractNumber($price);
                $prepaymentVal = self::extractNumber($prepaymentget);
                $rate = str_replace($priceVal, "", $price);

                $ostatok = self::toDouble($priceVal) - self::toDouble($prepaymentVal);
                $ostatok = strval($ostatok) . $rate;
            }
            else
            {
                $prepaymentget = "";
                $ostatok = "";
            }
            $text = "Проверьте данные\n\n" .
                "<b>Кто: </b>$who\n" .
                "<b>Имя: </b>$name\n" .
                "<b>Номер телефона: </b>$phone\n" .
                "<b>Как связаться: </b>$typecontact\n" .
                "<b>Источник: </b>$source\n" .
                "<b>Бренд: </b>$brand\n" .
                "<b>Комплектация: </b>$complexion\n" .
                "<b>Агент: </b>$agent\n" .
                "<b>Стоимость: </b>$price\n" .
                "<b>Заход: </b>$zahod\n" .
                "<b>Прибыль: </b>$pribil\n";
            if ($prepayment == "Да")
            {
                $text .= "<b>Предоплата: </b>$prepaymentget\n" .
                    "<b>Остаток: </b>$ostatok\n";
            }
            $text .= "<b>Город: </b>$city\n" .
                "<b>Набор: </b>$type\n" .
                "<b>Доп. комментарий: </b>$additionalcomment";

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
