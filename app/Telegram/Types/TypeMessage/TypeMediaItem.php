<?php

namespace App\Telegram\Types\TypeMessage;


class TypeMediaItem
{
    /**
     * attach file or file_id
     * @var string
     */
    public string $type; //Type of the result, must be photo|video|animation|audio|document

    /**
     * @var string
     */
    public string $media;

    /**
     * Optional. Caption of the audio to be sent, 0-1024 characters after entities parsing
     * @var string
     */
    public string $caption;

    /**
     * Optional. Mode for parsing entities
     * @var string
     */
    public string $parse_mode = 'HTML';

    /**
     * Optional. Pass True if the animation needs to be covered with a spoiler animation
     * @var bool
     */
    public bool $has_spoiler;

    public function __construct(array $mediaItem)
    {
        //only standart text buttons
        try
        {
            if (!isset($mediaItem['type']) || !isset($mediaItem['file_id'])) return;
            $this->type = $mediaItem['type'];
            $this->media = $mediaItem['file_id'];
            if (isset($mediaItem['text']))
                $this->caption = $mediaItem['text'];
            if (isset($mediaItem['has_spoiler']))
                $this->has_spoiler = $mediaItem['has_spoiler'];

        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
    }

    public function get(): array
    {
        //TODO обработка пустой клавиатуры, возможность создать пустую клавиатуру

        try
        {
            if (empty($this->type) || empty($this->media)) return [];

            $arr =
                ['type'       => $this->type,
                 'media'      => $this->media,
                 'parse_mode' => $this->parse_mode,
                ];
            if (!empty($this->caption))
                $arr['caption'] = mb_substr($this->caption, 0, 1000);
            if (!empty($this->has_spoiler))
                $arr['has_spoiler'] = $this->has_spoiler;
            return $arr;
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return [];

    }
}
