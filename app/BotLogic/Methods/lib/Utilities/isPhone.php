<?php

namespace App\BotLogic\Methods\lib\Utilities;

class isPhone
{
    public static function handle(string $msg): string|null
    {
        $msg = trim($msg);
        $msg = str_replace([" ", "-", "{", "}", "(", ")", "_", ""], "", $msg);
        if (mb_substr($msg, 0, 2) == "+7")
        {
            $msg = "8" . mb_substr($msg, 2, mb_strlen($msg) - 2);
        }
        else if (mb_substr($msg, 0, 1) == "7")
        {
            $msg = "8" . mb_substr($msg, 1, mb_strlen($msg) - 1);
        }
        if (is_numeric($msg) && mb_strlen($msg) == 11)
        {
            return $msg;
        }
        return null;
    }
}
