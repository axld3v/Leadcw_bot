<?php

namespace App\Telegram\Methods\Bot;

use App\Telegram\Types\TelegramRequest\TelegramMethods;

class DeleteMyCommands extends TelegramMethods
{
    public string $methodName = "DeleteMyCommands";

    /**
     * Optional
     * A JSON-serialized object, describing the scope of users for which the commands are relevant.
     * @var array|null
     */
    public array|null $scope;

    /**
     * Optional
     * A two-letter ISO 639-1 language code. If empty, commands will be applied to all users from the given scope, for whose language there are no dedicated commands.
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
