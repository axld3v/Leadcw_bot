<?php

namespace App\Telegram\Types\TelegramRequest;

class TelegramResult
{
    public bool $send;
    public bool $isError = false;
    public string $error;
    public mixed $result = [];
    public TelegramResponse $response;
    private array $messages_id = [];

    public function __construct(TelegramResponse $response)
    {
        try
        {
            $this->response = $response;
            $this->send = !empty($response->resultData);
            if (!empty($response->errorInfo))
            {
                $this->error = $response->errorInfo;
                $this->isError = true;
            }

            if (!$this->send) return;
            if (isset($response->resultData))
                $this->result = $response->resultData;

            if (isset($this->result['message_id']))
                $this->messages_id[] = $this->result['message_id'];
            else if (is_array($this->result))
            {
                foreach ($this->result as $result_item)
                {
                    if (!isset($result_item['message_id'])) continue;
                    $this->messages_id[] = $result_item['message_id'];
                }
            }
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
    }

    public function getMessagesId(): array
    {
        return $this->messages_id ?? [];
    }

    public function getMessageId(): int
    {
        return $this->messages_id[0] ?? 0;
    }
}
