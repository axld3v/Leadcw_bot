<?php

namespace App\Telegram\Methods\Message;

use App\Models\User;
use App\Telegram\Types\Keyboard\KeyboardTypes;
use App\Telegram\Types\TelegramRequest\TelegramMethods;

class SendPhoto extends TelegramMethods
{
    public string $methodName = "SendPhoto";
    /**
     * Unique identifier for the target chat or username of the target channel (in the format @channelusername)
     * @var User|string
     */
    public User|string $chat_id = '';

    /**
     * Text of the message to be sent
     * @var string
     */
    public string $caption = '';

    /**
     * Photo to send. Pass a file_id as String to send a photo that exists on the
     * Telegram servers (recommended) or URL Photo
     * @var string
     */
    public string $photo = '';

    /**
     * Optional. Mode for parsing entities in the message text. See formatting options for more details.
     *
     * @var string
     */
    public string $parse_mode = '';

    /**
     * Optional. Unique identifier for the target message thread (topic) of the forum; for forum supergroups only
     * @var int
     */
    public int $message_thread_id;

    /**
     * Optional. Disables link previews for links in this message
     * @var bool
     */
    public bool $disable_web_page_preview;

    /**
     * Optional. Sends the message silently.
     * @var bool
     */
    public bool $disable_notification;

    /**
     * Optional. Protects the contents of the sent message from forwarding and saving
     *
     * @var bool
     */
    public bool $protect_content;

    /**
     * Optional. If the message is a reply, ID of the original message
     * @var int
     */
    public int $reply_to_message_id;

    public string $business_connection_id;

    /**
     * Optional. Additional interface options. A JSON-serialized object for a custom reply keyboard, instructions to
     * hide keyboard or to force a reply from the user
     * @var KeyboardTypes|array
     */
    public KeyboardTypes|array $reply_markup = [];


    public function __construct(User|string $user = null, bool $clear = false)
    {
        //set default settings
        if ($clear) return;
        $this->parse_mode = 'HTML';
        $this->disable_web_page_preview = true;
        if (!is_null($user)) $this->chat_id = $user;
    }

    public function formatting(): void
    {
        $this->caption = mb_substr($this->caption, 0, 1000);
    }
}
