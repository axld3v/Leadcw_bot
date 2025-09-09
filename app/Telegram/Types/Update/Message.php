<?php

namespace App\Telegram\Types\Update;

use App\Telegram\Types\Keyboard\KeyboardInline;
use App\Telegram\Utilities\EntityDecoder;

class Message extends UpdateType
{
    public string $type = "Message";
    public bool $isGroup;
    public bool $isBackMessage = false;
    public bool $isBusiness = false;

    public array $jsonData;
    public int $message_id;
    public string $chat_id;

    public array $chat;
    public array $from;

    public ?KeyboardInline $reply_markup;

    public ?int $message_thread_id;
    public ?int $reply_to_message_id;

    public int $date_timestamp;

    public string $message = "";
    public string $msg = "";

    public string $text = "";
    public string $text_html = "";

    public bool $isMedia = false;
    public array $file = []; //type => '', file_id=>''

    public string $business_connection_id;
    public bool $isBotMessage = false;

    public function __construct(array $update)
    {
        try
        {
            $this->jsonData = $update;
            $this->message_id = $update['message_id'];
            $this->message_thread_id = $update['message_thread_id'] ?? null;
            $this->reply_to_message_id = $update['reply_to_message']['message_id'] ?? null;
            $this->date_timestamp = $update['date'];

            if (isset($this->jsonData['from']['is_bot']))
                $this->isBotMessage = $this->jsonData['from']['is_bot'];

            if (isset($update['business_connection_id']))
            {
                $this->business_connection_id = $update['business_connection_id'];
                $this->isBusiness = true;
            }

            if (isset($update['chat']) && is_array($update['chat'])) $this->chat = $update['chat'];
            if (isset($update['from']) && is_array($update['from'])) $this->from = $update['from'];

            $this->isGroup = isset($update['chat']['id']) && str_contains($update['chat']['id'], "-");
            $this->chat_id = $update['chat']['id'];
            if (isset($update['reply_markup']['inline_keyboard']))
                $this->reply_markup = new KeyboardInline($update['reply_markup']['inline_keyboard']) ?? null;

            if (isset($update['caption']))
            {
                $this->text = $update['caption'];
                $this->text_html = empty($update['caption_entities'])
                    ? $this->text
                    : EntityDecoder::entitiesToHtml($update['caption'], $update['caption_entities']);
            }
            else if (isset($update['text']))
            {
                $this->text = $update['text'];
                $this->text_html = empty($update['entities'])
                    ? $this->text
                    : EntityDecoder::entitiesToHtml($update['text'], $update['entities']);
            }
            else if (isset($update['contact']))
            {
                $this->text = $update['contact']['phone_number'];
                $this->text_html = empty($update['entities'])
                    ? $this->text
                    : EntityDecoder::entitiesToHtml($update['text'], $update['entities']);
            }

            if (isset($this->text))
            {
                $this->message = mb_strtolower($this->text);
                $this->msg = $this->text;
            }


            //check media
            $file = $this->getFileByMessage($update);
            if (!is_null($file))
            {
                $this->isMedia = true;
                $this->file = $file;
            }
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
    }

    private function getFileByMessage(array $update): array|null
    {
        $file = [];
        try
        {
            if (array_key_exists("photo", $update))
                $file = ['type' => 'photo', 'file_id' => end($update['photo'])['file_id']];
            else if (array_key_exists("document", $update))
                $file = ['type' => 'document', 'file_id' => $update['document']['file_id']];
            else if (array_key_exists("video", $update))
                $file = ['type' => 'video', 'file_id' => $update['video']['file_id']];
            else if (array_key_exists("video_note", $update))
                $file = ['type' => 'video_note', 'file_id' => $update['video_note']['file_id']];
            else if (array_key_exists("audio", $update))
                $file = ['type' => 'audio', 'file_id' => $update['audio']['file_id']];
            else if (array_key_exists("voice", $update))
                $file = ['type' => 'voice', 'file_id' => $update['voice']['file_id']];
            if (empty($file))
                return null;
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return $file;
    }
}
