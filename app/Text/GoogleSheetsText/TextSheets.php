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
                'date_last_update'   => date("Y-m-d H:i:s"),
                'start'              => $text_arr[0][0],
                'who'                => [
                    'text'  => $text_arr[1][0] ?? "",
                    'items' => self::clearItems($text_arr[2] ?? [])
                ],
                'name'               => ['text' => $text_arr[4][0] ?? "",],
                'phone'              => ['text' => $text_arr[5][0] ?? "",],
                'typecontact'       => [
                    'text'  => $text_arr[7][0] ?? "",
                    'items' => self::clearItems($text_arr[8] ?? [])
                ],
                'source'             => [
                    'text'  => $text_arr[10][0] ?? "",
                    'items' => self::clearItems($text_arr[11] ?? [])
                ],
                'brand'              => [
                    'text'  => $text_arr[13][0] ?? "",
                    'items' => self::clearItems($text_arr[14] ?? [])
                ],
                'complexion'         => ['text' => $text_arr[16][0] ?? "",],
                'agent'              => [
                    'text'  => $text_arr[18][0] ?? "",
                    'items' => self::clearItems($text_arr[19] ?? [])
                ],
                'price'              => ['text' => $text_arr[21][0] ?? "",],
                'prepayment'         => [
                    'text'  => $text_arr[23][0] ?? "",
                    'items' => self::clearItems($text_arr[24] ?? [])
                ],
                'prepaymentget'     => ['text' => $text_arr[26][0] ?? "",],
                'city'               => [
                    'text'  => $text_arr[28][0] ?? "",
                    'items' => self::clearItems($text_arr[29] ?? [])
                ],
                'type'               => [
                    'text'  => $text_arr[31][0] ?? "",
                    'items' => self::clearItems($text_arr[32] ?? [])
                ],
                'additionalcomment' => [
                    'text'  => $text_arr[34][0] ?? "",
                ],
                'zahod' => [
                    'text'  => $text_arr[36][0] ?? "",
                ],
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
