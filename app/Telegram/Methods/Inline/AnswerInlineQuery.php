<?php

namespace App\Telegram\Methods\Inline;

use App\Telegram\Types\TelegramRequest\TelegramMethods;

class AnswerInlineQuery extends TelegramMethods
{
    /**
     * Название метода API
     *
     * @var string
     */
    public string $methodName = "answerInlineQuery";

    /**
     * Unique identifier for the answered query
     *
     * @var string
     */
    public string $inline_query_id;

    /**
     * A JSON-serialized array of results for the inline query
     *
     * @var array
     */
    public array $results = [];

    /**
     * Optional. The maximum amount of time in seconds that the result of the inline query may be cached on the server.
     * Defaults to 300.
     *
     * @var int
     */
    public int $cache_time = 300;

    /**
     * Optional. Pass True if results may be cached on the server side only for the user that sent the query.
     *
     * @var bool
     */
    public bool $is_personal = false;

    /**
     * Optional. Pass the offset that a client should send in the next query with the same text to receive more results.
     *
     * @var string
     */
    public string $next_offset = '';

    /**
     * Optional. If passed, clients will display a button with specified text that switches the user to a private chat
     * with the bot and sends the bot a start message with the parameter switch_pm_parameter.
     *
     * @var string
     */
    public string $switch_pm_text = '';

    /**
     * Optional. Deep-linking parameter for the /start message sent to the bot when user presses the switch button.
     *
     * @var string
     */
    public string $switch_pm_parameter = '';

    /**
     * Конструктор для установки базовых параметров
     *
     * @param string $query_id
     * @param array $results
     * @param int $cache_time
     */
    public function __construct(string $query_id = "", array $results = [], int $cache_time = 300)
    {
        if (empty($query_id)) return;
        $this->inline_query_id = $query_id;
        $this->results = $results;
        $this->cache_time = $cache_time;
    }

}
