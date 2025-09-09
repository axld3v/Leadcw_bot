<?php

namespace App\Telegram\Types\Keyboard;

use Illuminate\Support\Collection;

class KeyboardInline extends KeyboardTypes
{
    public bool $isError = false;
    public string $error;
    protected Collection $keyboard;

    public function __construct(array|Collection $buttons, int $columns = 1, string $default_type = "callback_data")
    {
        //only standart callback buttons
        try
        {
            if (is_array($buttons))
            {
                $buttons = collect($buttons);
            }
            $collection = $buttons
                ->map(function ($item) use ($default_type) {
                    return $this->ensureTextKey($item, $default_type);
                });
            if (empty($collection->first()))
            {
                $this->isError = true;
                $this->error = "empty keyboard";
                return;
            }
            $this->keyboard = $collection;
            try
            {
                $collection = $this->cleanCollection($collection);

                //Нужно ли разделять кнопки по столбцам
                $first_key = array_keys($buttons->first())[0];
                if (isset($buttons->first()[$first_key]) && !is_array($buttons->first()[$first_key]))
                {
                    $collection = $collection->chunk($columns)
                        ->map(function ($chunk) {
                            return $chunk->values();   // This "resets" the indices of all chunks
                        });
                }
                $this->keyboard = $collection;
            } catch (\Throwable $throwable)
            {
                logging($throwable);
            }
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
    }

    private function ensureTextKey($item, $base_action)
    {
        // Если $item является массивом, обрабатываем каждый элемент
        try
        {
            if (is_array($item) && (isset($item[0]) && is_array($item[0])))
            {
                // Рекурсивно применяем функцию ensureTextKey к каждому элементу массива
                if (array_key_exists("text", $item))
                    return $item;
                return array_map(function ($subItem) use ($base_action) {
                    return $this->ensureTextKey($subItem, $base_action);
                }, $item);
            }
            else if (is_array($item))
            {
                if (array_key_exists("text", $item))
                    return $item;

                if (count($item) == 2)
                    return ['text' => $item[0], $base_action => $item[1]];

                if (count($item) == 1)
                {
                    if (is_numeric(array_keys($item)[0])) return [];
                    return ['text' => array_keys($item)[0], $base_action => $item[array_keys($item)[0]]];
                }
            }
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return [];
    }

    public function deleteButtonByName(string $name, bool $exactMatch = false): KeyboardTypes
    {
        try
        {
            $collection = $this->keyboard->map(function ($subCollection) use ($name, $exactMatch) {
                // Проверяем, является ли подколлекция коллекцией или массивом
                if ($subCollection instanceof Collection)
                {
                    // Если это коллекция, используем метод reject
                    return $subCollection->reject(function ($item) use ($name, $exactMatch) {
                        return $exactMatch
                            ? isset($item['text']) && mb_strtolower($item['text']) === mb_strtolower($name)
                            : isset($item['text']) && $item['text'] === $name;
                    });
                }
                elseif (is_array($subCollection))
                {
                    // Если это массив, используем функцию array_filter
                    return array_filter($subCollection, function ($item) use ($name, $exactMatch) {
                        return !($exactMatch
                            ? isset($item['text']) && mb_strtolower($item['text']) === mb_strtolower($name)
                            : isset($item['text']) && $item['text'] === $name);
                    });
                }
                else
                {
                    // Если это ни массив, ни коллекция, возвращаем как есть
                    return $subCollection;
                }
            });
            $this->keyboard = $this->cleanCollection($collection);
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
            return [
                'inline_keyboard' => $this->keyboard->toArray(),
            ];
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return [];
    }
}
