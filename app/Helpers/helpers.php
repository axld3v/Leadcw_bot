<?php

if (!function_exists('logging'))
{
    function logging(string|\Stringable $message, array $context = [], bool $warning = false): void
    {
        if ($warning)
            Log::warning($message, $context);
        else
            Log::error($message, $context);
        if (!config("app.debug")) return;
        if (is_string($message))
        {
            echo $message . "\n";
            return;
        }

        echo "ERROR (" . $message->getMessage() . ") \n" .
            "file: " . $message->getFile() . " \n" .
            "line: " . $message->getLine() . " \n\n";
    }
}
