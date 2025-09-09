<?php

namespace App\Text\GoogleSheetsText;

class TextSheets
{
    public static function handle(): void
    {
        try
        {
            $sheets = new \App\Services\GoogleSheetsService();
            $text_arr = $sheets->getValueCells("Текста", "B3:Z");

            $json = [
                'date_last_update' => date("Y-m-d H:i:s"),
                'start'            => $text_arr[0][0],
                'date'             => [
                    'text'  => $text_arr[2][0] ?? "",
                    'items' => self::clearItems($text_arr[3] ?? [])
                ],
                'requests_tg'      => [
                    'text'  => $text_arr[5][0] ?? "",
                    'items' => self::clearItems($text_arr[6] ?? [])
                ],
                'requests_inst'    => [
                    'text'  => $text_arr[8][0] ?? "",
                    'items' => self::clearItems($text_arr[9] ?? [])
                ],
                'requests_wat'     => [
                    'text'  => $text_arr[11][0] ?? "",
                    'items' => self::clearItems($text_arr[12] ?? [])
                ],
                'requests_all'     => [
                    'text'  => $text_arr[14][0] ?? "",
                    'items' => self::clearItems($text_arr[15] ?? [])
                ],
                'check'            => $text_arr[17][0] ?? "",
            ];
            $jsonData = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $ok = file_put_contents(storage_path('text/text.json'), $jsonData);
            if ($ok) echo "success";
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
    }

    private static function clearItems(array $items): array
    {
        $itemsRez = [];
        foreach ($items as $item)
        {
            if (empty($item)) continue;
            $itemsRez[] = $item;
        }
        return $itemsRez;
    }
}
