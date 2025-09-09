<?php

namespace App\Telegram\Methods\Webhook;

use App\Telegram\Types\TelegramRequest\TelegramMethods;
use App\Telegram\Types\TelegramRequest\TelegramResult;

class DeleteWebhook extends TelegramMethods
{
    public string $methodName = "DeleteWebhook";

    /**
     * Pass True to drop all pending updates
     * @var bool
     */
    public bool $drop_pending_updates = false;

    static function result(TelegramResult $result): null|array
    {
        $jsonResponse = $result->response->decodedData;
        if (!is_array($jsonResponse) || empty($jsonResponse)) return null;
        $info = "";
        $status = isset($jsonResponse['result']) && $jsonResponse['result'] === true
            ? "success"
            : "error";

        if (isset($jsonResponse['description']))
        {
            $info = $jsonResponse['description'];
        }
        return ['info' => $info, 'status' => $status];
    }
}
