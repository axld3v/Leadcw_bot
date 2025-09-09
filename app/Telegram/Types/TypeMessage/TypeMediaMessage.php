<?php

namespace App\Telegram\Types\TypeMessage;

class TypeMediaMessage
{
    public array $mediaItems = [];

    public function __construct(array $media)
    {
        //only standart text buttons
        try
        {
            if (!isset($media['media']))
                return;
            for ($i = 0; $i < count($media['media']); $i++)
            {
                $mediaItem = $media['media'][$i];
                if ($i == 0 && isset($media['text']))
                    $mediaItem['text'] = $media['text'];
                $item = (new TypeMediaItem($mediaItem))->get();
                if (empty($item)) continue;
                $this->mediaItems[] = $item;
            }
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
    }
}
