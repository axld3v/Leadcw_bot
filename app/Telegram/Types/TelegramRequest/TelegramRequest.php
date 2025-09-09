<?php

namespace App\Telegram\Types\TelegramRequest;

class TelegramRequest
{
    public string $methodName;
    public array $fieldsArray;

    public function __construct(TelegramMethods $method)
    {
        $this->methodName = $method->methodName;
        $this->fieldsArray = $method->export();
    }
}
