<?php

namespace App\Telegram\Methods\Message;

use App\Models\User;
use App\Telegram\Types\Keyboard\KeyboardTypes;
use App\Telegram\Types\TelegramRequest\TelegramMethods;

/**
 * This class represents the editMessageText method in the Telegram Bot API.
 */
class EditMessageCaption extends TelegramMethods
{
    public string $methodName = "EditMessageCaption";
    /**
     * Optional
     * Unique identifier of the business connection on behalf of which the message to be edited was sent
     * @var string|null
     */
    public string $business_connection_id;

    /**
     * Optional
     * Unique identifier for the target chat or username of the target channel (in the format @channelusername)
     * @var string|User
     */
    public string|User $chat_id;

    /**
     * Optional
     * Identifier of the message to edit
     * @var int|null
     */
    public int $message_id;

    /**
     * Optional
     * Identifier of the inline message
     * @var string|null
     */
    public string $inline_message_id;

    /**
     * Required
     * New text of the message, 1-1000 characters after entities parsing
     * @var string
     */
    public string $caption;

    /**
     * Optional
     * Mode for parsing entities in the message text. See formatting options for more details.
     * @var string|null
     */
    public string $parse_mode;

    /**
     * Optional
     * A JSON-serialized list of special entities that appear in message text, which can be specified instead of parse_mode
     * @var array|null
     */
    public array $caption_entities;

    /**
     * Optional
     * Link preview generation options for the message
     * @var bool|null
     */
    public bool $link_preview_options;

    /**
     * Optional
     * A JSON-serialized object for an inline keyboard.
     * @var KeyboardTypes|array|null
     */
    public KeyboardTypes|array|null $reply_markup;

    public bool $disable_web_page_preview;

    /**
     * Constructor with optional user and clear flag.
     *
     * @param User|string|null $user
     * @param bool $clear
     */
    public function __construct(User|string $user = null, bool $clear = false)
    {
        // Set default settings
        if ($clear) return;
        $this->parse_mode = 'HTML';
        $this->disable_web_page_preview = true;
        if (!is_null($user)) $this->chat_id = $user;
    }
}
