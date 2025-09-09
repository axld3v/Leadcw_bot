<?php

namespace App\Telegram\Methods\Bot;
use App\Telegram\Types\TelegramRequest\TelegramMethods;

/**
 * This class represents the setMyName method in the Telegram Bot API.
 */
class SetMyName extends TelegramMethods
{
    public string $methodName = "setMyName";

    /**
     * Optional
     * New bot name; 0-64 characters. Pass an empty string to remove the dedicated name for the given language.
     * @var string|null
     */
    public string $name;

    /**
     * Optional
     * A two-letter ISO 639-1 language code. If empty, the name will be shown to all users for whose language there is no dedicated name.
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
