<?php

namespace App\Telegram\Methods\Bot;
use App\Telegram\Types\TelegramRequest\TelegramMethods;
use App\Telegram\Types\BotCommand;
use App\Telegram\Types\BotCommandScope;

/**
 * This class represents the setMyCommands method in the Telegram Bot API.
 */
class SetMyCommands extends TelegramMethods
{
    public string $methodName = "setMyCommands";

    /**
     * Required
     * A JSON-serialized list of bot commands to be set as the list of the bot's commands. At most 100 commands can be specified.
     * @var array
     */
    public array $commands;

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
