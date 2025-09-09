<?php

namespace App\Telegram\Methods\Webhook;

use App\Telegram\Types\TelegramRequest\TelegramMethods;
use App\Telegram\Types\TelegramRequest\TelegramResult;

class getWebhookInfo extends TelegramMethods
{
    public string $methodName = "getWebhookInfo";

    static function result(TelegramResult $result): null|array
    {
        $jsonResponse = $result->response->decodedData ?? null;
        if (!is_array($jsonResponse) || empty($jsonResponse)) return null;
        $info = "";
        $status = !empty($jsonResponse['result'])
            ? "success"
            : "error";

        if (isset($jsonResponse['result']))
        {
            if (isset($jsonResponse['result']['url']))
                $info .= "Подключенный сайт: " . $jsonResponse['result']['url'] . "\n" ?? "\n";
            if (isset($jsonResponse['result']['pending_update_count']))
                $info .= "Необработанных обновлений: " . $jsonResponse['result']['pending_update_count'] . "\n" ?? "\n";
            if (isset($jsonResponse['result']['last_error_message']) && isset($jsonResponse['result']['last_error_date']))
                $info .= "Последняя ошибка: {$jsonResponse['result']['last_error_message']} ({$jsonResponse['result']['last_error_date']})\n";
            if (isset($jsonResponse['result']['max_connections']))
                $info .= "Количество одновременных запросов: {$jsonResponse['result']['max_connections']}\n";
        }
        return ['info' => $info, 'status' => $status];
    }
}
