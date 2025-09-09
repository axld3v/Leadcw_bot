<?php

namespace App\Telegram\Utilities;

class ShowTextResult
{
    public static function print(array|null $info, bool $showTags = true): void
    {
        try
        {
            if (is_null($info)) return;
            if (array_key_exists("status", $info))
            {
                if ($showTags)
                    echo "status: " . $info['status'] . "\n";
            }
            if (array_key_exists("info", $info))
            {
                if ($showTags)
                    echo 'info: ';

                if (is_array($info['info']))
                    $info = json_encode($info, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                echo $info['info'] . "\n\n";
            }
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
    }
}
