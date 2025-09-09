<?php

namespace App\Telegram\Methods\Bot;

use App\Telegram\Types\TelegramRequest\TelegramMethods;


/**
 * This class represents the setMyShortDescription method in the Telegram Bot API.
 */
class SetMyShortDescription extends TelegramMethods
{
    public string $methodName = "setMyShortDescription";

    /**
     * Optional
     * New short description for the bot; 0-120 characters. Pass an empty string to remove the dedicated short description for the given language.
     * @var string|null
     */
    public string $short_description;

    /**
     * Optional
     * A two-letter ISO 639-1 language code. If empty, the short description will be applied to all users for whose language there is no dedicated short description.
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
