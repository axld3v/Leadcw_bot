<?php

namespace App\Telegram\Methods\Message;

use App\Telegram\Types\TelegramRequest\TelegramMethods;
use App\Telegram\Types\TelegramRequest\TelegramResult;

class GetFile extends TelegramMethods
{
    public string $methodName = "getFile";
    /**
     * file_id
     * @var string
     */
    public string $file_id = '';

    public function __construct(bool $clear = false)
    {

    }
}
