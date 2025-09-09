<?php

namespace App\Telegram\Methods\Bot;

use App\Telegram\Types\TelegramRequest\TelegramMethods;

class GetMyCommands extends TelegramMethods
{
    public string $methodName = "getMyCommands";

    /**
     * Optional
     * A JSON-serialized object, describing the scope of users. Defaults to BotCommandScopeDefault.
     * @var array|null
     */
    public array|null $scope;

    /**
     * Optional
     * A two-letter ISO 639-1 language code or an empty string.
     * @var string|null
     */
    public string $language_code;

    /**
     * Constructor with optional clear flag.
     *
     * @param bool $clear
     */
    public function __construct(bool $clear = false)
    {
        // Set default settings
        if ($clear) return;
    }
}
