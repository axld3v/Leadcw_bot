<?php

namespace App\Telegram\Types\Keyboard;

use Illuminate\Support\Collection;
use InvalidArgumentException;

abstract class KeyboardTypes
{
    public bool $isError = false;
    public string $error;
    protected Collection $keyboard;

    public function insertRowAfterPosition(Collection|KeyboardTypes|array $insertCollection, int $position)
    {
        $position++;
        if (is_array($insertCollection)) $insertCollection = new KeyboardInline($insertCollection);
        if ($insertCollection instanceof KeyboardTypes)
            $insertCollection = $insertCollection->keyboard;
        // Проверка на валидность позиции
        if ($position < 0 || $position > $this->keyboard->count())
        {
            throw new InvalidArgumentException("Invalid position to insert collection.");
        }

        // Разделяем оригинальную коллекцию на две части
        $firstPart = $this->keyboard->slice(0, $position);
        $secondPart = $this->keyboard->slice($position);

        // Объединяем первую часть, вставляем коллекцию и затем объединяем со второй частью
        $this->keyboard = $firstPart->merge($insertCollection)->merge($secondPart);
        $asda = 0;
    }

    public final static function merge(KeyboardTypes $buttons_up, KeyboardTypes $buttons_down): KeyboardTypes
    {
        $buttons_up->addRow($buttons_down);
        return $buttons_up;
    }

    public final function addRow(KeyboardTypes|array $buttons, int $columns = 2, bool $position_down = true): KeyboardTypes
    {
        try
        {
            if (is_array($buttons))
                $collectionButtons = (new KeyboardInline($buttons, $columns))->getKeyboard();
            else
                $collectionButtons = $buttons->getKeyboard();
            if ($position_down)
                $this->keyboard = $this->keyboard->merge($collectionButtons);
            else
                $this->keyboard = $collectionButtons->merge($this->keyboard);
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return $this;
    }

    protected final function getKeyboard(): Collection
    {
        return $this->keyboard;
    }

    public function replaceValueWithCollection(string $value,Collection|KeyboardInline|array $insertCollection)
    {
        // Проверка, если значение существует в коллекции
        if (is_array($insertCollection)) $insertCollection = new KeyboardInline($insertCollection);
        if ($insertCollection instanceof KeyboardTypes)
            $insertCollection = $insertCollection->keyboard;

        $position = $this->keyboard->search($value);

        if ($position === false)
        {
            throw new InvalidArgumentException("Value not found in the collection.");
        }

        // Разделяем оригинальную коллекцию на две части
        $firstPart = $this->keyboard->slice(0, $position);
        $secondPart = $this->keyboard->slice($position + 1);

        // Объединяем первую часть, вставляем коллекцию и затем объединяем со второй частью
        $this->keyboard = $firstPart->merge($insertCollection)->merge($secondPart);
    }

    public final function customChunk(array $chunkSizes): KeyboardTypes
    {
        try
        {
            $chunks = [];
            $buttonsCollapse = $this->keyboard->collapse();
            foreach ($chunkSizes as $size)
            {
                $chunks[] = $buttonsCollapse->splice(0, $size);
            }
            $this->keyboard = collect($chunks);
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return $this;
    }

    public final function deleteRow(int $id_row): KeyboardTypes
    {
        try
        {
            $this->keyboard = $this->keyboard
                ->forget($id_row)
                ->map(function ($collection) {
                    return $collection->values();
                });
            $this->keyboard = $this->keyboard->values();
            //TODO обработка исключений для id_row больше count и для отрицательных
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return $this;
    }

    public function deleteButtonById(int $idToRemove): KeyboardTypes
    {
        $currentId = 0;
        try
        {
            // Фильтруем элементы внутри подколлекций
            $collection = $this->keyboard->map(function ($subCollection) use (&$currentId, $idToRemove) {
                return $subCollection->reject(function ($item) use (&$currentId, $idToRemove) {
                    $result = ($currentId === $idToRemove);
                    $currentId++;
                    return $result;
                });
            });
            $this->keyboard = $this->cleanCollection($collection);
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        //TODO обработка исключений для $idToRemove больше count и для отрицательных
        return $this;
    }

    protected final function cleanCollection($item)
    {
        try
        {
            if ($item instanceof Collection)
            {
                $item = $item->map(function ($subItem) {
                    return $this->cleanCollection($subItem);
                })->reject(function ($subItem) {
                    return $subItem === null || (is_array($subItem) && empty($subItem)) || ($subItem instanceof Collection && $subItem->isEmpty());
                });

                return $item->isEmpty() ? null : $item;
            }
            elseif (is_array($item))
            {
                foreach ($item as $key => $subItem)
                {
                    $item[$key] = $this->cleanCollection($subItem);
                    if ($item[$key] === null)
                    {
                        unset($item[$key]);
                    }
                }

                return empty($item) ? null : $item;
            }
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return $item;
    }

    abstract protected function deleteButtonByName(string $name): KeyboardTypes;

    abstract protected function get(): array;
}
