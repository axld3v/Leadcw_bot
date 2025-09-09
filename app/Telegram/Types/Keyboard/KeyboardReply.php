<?php

namespace App\Telegram\Types\Keyboard;

use Illuminate\Support\Collection;

class KeyboardReply extends KeyboardTypes
{
    /**
     * The keyboard will be hidden after use
     * @var bool
     */
    public bool $one_time_keyboard = false;
    /**
     * The placeholder to be shown in the input field when the keyboard is active; 1-64 characters.
     * @var string
     */
    public string $input_field_placeholder;
    /**
     * The keyboard will always be shown, buttons cannot be hidden
     * @var bool
     */
    public bool $is_persistent = false;
    /**
     * Resize the keyboard vertically for optimal fit
     * @var bool
     */
    public bool $resize_keyboard = true;
    /**
     * if show the keyboard to specific users only. Can usage
     * 1) users that are @ username in the text of the Message object;
     * 2) if the bot's message is a reply (has reply_to_message_id), sender of the original message.
     * @var bool
     */
    public bool $selective = false;
    protected Collection $keyboard;

    public function __construct(array|Collection $buttons, int $columns = 1, bool $one_time_keyboard = false)
    {
        //only standart text buttons
        try
        {
            if (is_array($buttons))
                $buttons = collect($buttons);
            $collection = $buttons
                ->map(function ($item) {
                    return $this->ensureTextKey($item);
                });
            //Нужно ли разделять кнопки по столбцам
            if (!(is_array($buttons->first()) && !array_key_exists("text", $buttons->first())))
            {
                $collection = $collection->chunk($columns)
                    ->map(function ($chunk) {
                        return $chunk->values();   // This "resets" the indices of all chunks
                    });
            }
            $this->keyboard = $collection;
            if ($one_time_keyboard)
                $this->one_time_keyboard = true;
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
    }

    private function ensureTextKey($item)
    {
        // Если $item является массивом, обрабатываем каждый элемент
        try
        {
            if (is_array($item))
            {
                // Рекурсивно применяем функцию ensureTextKey к каждому элементу массива
                if (array_key_exists("text", $item))
                    return $item;
                return array_map(function ($subItem) {
                    return $this->ensureTextKey($subItem);
                }, $item);
            }
            else
            {
                // Если $item не является массивом, добавляем ключ 'text'
                return ['text' => $item];
            }
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return $item;
    }

    public function deleteButtonByName(string $name, bool $exactMatch = false): KeyboardTypes
    {
        // Фильтруем элементы внутри подколлекций
        try
        {
            $collection = $this->keyboard->map(function ($subCollection) use ($name, $exactMatch) {
                return $subCollection->reject(function ($item) use ($name, $exactMatch) {
                    return $exactMatch
                        ? isset($item['text']) && mb_strtolower($item['text']) === mb_strtolower($name)
                        : isset($item['text']) && $item['text'] === $name;
                });
            });

            $this->keyboard = $collection;
            $this->cleanCollection($this->keyboard);
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return $this;
    }

    public function get(): array
    {
        try
        {
            $arr = [
                'keyboard'          => $this->keyboard->toArray(),
                'resize_keyboard'   => $this->resize_keyboard,
                'one_time_keyboard' => false,
                'selective'         => $this->selective,
                'is_persistent'     => true,
            ];
            if (!empty($input_field_placeholder))
                $arr['input_field_placeholder'] = $input_field_placeholder;
            return $arr;
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return [];

    }
}
