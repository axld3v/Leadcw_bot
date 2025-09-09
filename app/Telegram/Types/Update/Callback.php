<?php

namespace App\Telegram\Types\Update;

class Callback extends UpdateType
{
    public string $type = "Callback";
    public bool $isGroup;
    public bool $isBusiness;

    public array $rawData;

    public int $message_id;
    public int $date_timestamp;

    public string $id_callback = "";

    public string $callback_data = ""; //callback кнопки
    public string $button = ""; //callback кнопки
    public string $text_button = ""; //Название кнопки

    public ?Message $message;

    public function __construct(array $update)
    {
        try
        {
            $this->message = new Message($update['message']) ?? null;
            $this->isGroup = $this->message->isGroup;
            $this->callback_data = $update['data'];
            $this->button = mb_strtolower($update['data']);

            $this->isBusiness = $this->message->isBusiness;

            $this->rawData = $update;

            $this->message_id = $update['message']['message_id'];
            $this->id_callback = $update['id'];
            $this->date_timestamp = $update['message']['date'];

            foreach ($update['message']['reply_markup']['inline_keyboard'] as $btn_row)
            {
                foreach ($btn_row as $btn_col)
                {
                    if (isset($btn_col['callback_data']) && mb_strtolower($btn_col['callback_data']) != mb_strtolower($this->callback_data)) continue;
                    $this->text_button = $btn_col['text'];
                    break;
                }

                if (!empty($this->text_button)) break;
            }
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
    }
}
