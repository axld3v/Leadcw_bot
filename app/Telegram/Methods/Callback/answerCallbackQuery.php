<?php

namespace App\Telegram\Methods\Callback;

use App\Telegram\Types\TelegramRequest\TelegramMethods;
use App\Telegram\Types\TelegramRequest\TelegramResult;

class answerCallbackQuery extends TelegramMethods
{
    public string $methodName = "answerCallbackQuery";
    /**
     * Unique identifier for the query to be answered
     * @var string
     */
    public string $callback_query_id = '';

    /**
     * Text of the message to be sent
     * @var string
     */
    public string $text = '';

    /**
     * If True, an alert will be shown by the client instead of a notification at the top of the chat screen. Defaults to false.
     * @var bool
     */
    public bool $show_alert;

    /**
     * The maximum amount of time in seconds that the result of the callback query may be cached client-side.
     * Telegram apps will support caching starting in version 3.14. Defaults to 0.
     * seconds
     * @var int
     */
    public int $cache_time;

    public function __construct(bool $clear = false)
    {
        //set default settings
        if ($clear) return;
    }

    static function result(TelegramResult $result)
    {
        // TODO: Implement result() method.
    }

    public function formatting(): void
    {
        $this->text = mb_substr($this->text, 0, 200);
    }
}
