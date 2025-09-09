<?php

namespace App\Telegram\Types\Update;

class Query extends UpdateType
{
    public string $type = "Query";
    public bool $isGroup;

    public array $jsonData;
    public string $id_query;
    public string $chat_id;

    public string $query;
    public string $offset;


    public array $from;

    public bool $isBotMessage = false;

    public function __construct(array $update)
    {
        try
        {
            $this->jsonData = $update;
            if (isset($this->jsonData['from']['is_bot']))
                $this->isBotMessage = $this->jsonData['from']['is_bot'];
            if (isset($update['from']) && is_array($update['from'])) $this->from = $update['from'];
            $this->isGroup = isset($update['from']['id']) && str_contains($update['from']['id'], "-");

            if (isset($update['query'])) $this->query = $update['query'];
            if (isset($update['offset'])) $this->offset = $update['offset'];

            if (isset($update['id'])) $this->id_query = $update['id'];

            if (isset($update['chat_type']) && str_contains($update['chat_type'], 'group'))
                $this->isGroup = true;
            else
                $this->isGroup = false;

        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
    }
}
