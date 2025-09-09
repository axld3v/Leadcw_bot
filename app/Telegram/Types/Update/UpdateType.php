<?php

namespace App\Telegram\Types\Update;

use Exception;

abstract class UpdateType
{
    public string $type;
    public int $message_id;
    public bool $isGroup;
    public bool $isBackMessage = false;
    public bool $isBusiness;
    public array $jsonData;

    /**
     * @throws Exception
     */
    final function getTypeMessage(): Message
    {
        if ($this instanceof Message) return $this; // Уже является Message
        else throw new Exception("Неизвестный тип обновления");
    }

    /**
     * @throws Exception
     */
    final function getTypeCallback(): Callback
    {
        if ($this instanceof Callback) return $this; // Уже является Callbacl
        else throw new Exception("Неизвестный тип обновления");
    }

    final function getTypeQuery(): Query
    {
        if ($this instanceof Query) return $this; // Уже является Query
        else throw new Exception("Неизвестный тип обновления");
    }
}
