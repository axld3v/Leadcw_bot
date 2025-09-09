<?php

namespace App\Text;
class getText
{
    static array $jsonSheetsData;

    public static function getBySheets(string $title): string|array|null
    {
        try
        {
            if (empty(self::$jsonSheetsData))
            {
                if (!file_exists(storage_path('/text/text.json')))
                {
                    self::$jsonSheetsData = [];
                    return "";
                }
                $ourData = file_get_contents(storage_path('/text/text.json'));
                self::$jsonSheetsData = json_decode($ourData, true);
            }
            return self::$jsonSheetsData[$title];
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return null;
    }
}
