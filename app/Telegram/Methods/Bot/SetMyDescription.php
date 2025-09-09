<?php

namespace App\Telegram\Methods\Bot;

use App\Telegram\Types\TelegramRequest\TelegramMethods;

class SetMyDescription extends TelegramMethods
{
    public string $methodName = "setMyDescription";

    /**
     * Optional
     * New bot description; 0-512 characters. Pass an empty string to remove the dedicated description for the given language.
     * @var string|null
     */
    public string $description;

    /**
     * Optional
     * A two-letter ISO 639-1 language code. If empty, the description will be applied to all users for whose language there is no dedicated description.
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
