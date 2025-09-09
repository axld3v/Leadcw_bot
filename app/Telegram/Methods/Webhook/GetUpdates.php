<?php

namespace App\Telegram\Methods\Webhook;

use App\Telegram\Types\TelegramRequest\TelegramMethods;
use App\Telegram\Types\TelegramRequest\TelegramResult;

class GetUpdates extends TelegramMethods
{
    public string $methodName = "GetUpdates";
    public int $offset = 0;

    static function result(TelegramResult $result)
    {
        // TODO: Implement result() method.
    }
}
