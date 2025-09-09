<?php

namespace App\Telegram\Types\Keyboard;

use Illuminate\Support\Collection;

class KeyboardRemoveReply extends KeyboardTypes
{
    public bool $selective = false;
    protected Collection $keyboard;

    public function __construct()
    {

    }

    public function get(): array
    {
        try
        {
            return [
                'remove_keyboard' => true
            ];
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return [];

    }

    protected function deleteButtonByName(string $name): KeyboardTypes
    {
        return $this;
    }
}
