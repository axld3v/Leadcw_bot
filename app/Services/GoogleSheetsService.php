<?php

namespace App\Services;

use Google\Client;
use Google\Service\Exception;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use Google_Service_Sheets_BatchUpdateSpreadsheetRequest;
use Google_Service_Sheets_CellData;
use Google_Service_Sheets_CellFormat;
use Google_Service_Sheets_ExtendedValue;
use Google_Service_Sheets_Request;
use Google_Service_Sheets_RowData;
use Google_Service_Sheets_TextFormat;

class GoogleSheetsService
{
    private string $spreadsheetId;
    private Sheets $service;
    private array $sheets_id = [];
    private array $temp_requests = [];

    public function __construct(string $spreadsheetId = "")
    {
        if (config("app.debug")) echo "GoogleSheetsCreated\n";
        //load credentials google account
        if (empty($spreadsheetId)) $spreadsheetId = config("credentials.sheets.spreadsheet_id");

        $fileName = config('credentials.sheets.credentials');
        $pathToFile = storage_path("secrets/$fileName");
        if (!file_exists($pathToFile))
        {
            echo "not found google credentials";
            return;
        }
        $googleAccountKeyFilePath = $pathToFile;
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $googleAccountKeyFilePath);
        $client = new Client();
        $client->useApplicationDefaultCredentials();
        $client->addScope('https://www.googleapis.com/auth/spreadsheets');

        $this->service = new Sheets($client);
        $this->spreadsheetId = $spreadsheetId;
    }

    public function getValueCell(string $nameList, string $range): string
    {
        return $this->getValueCells($nameList, $range)[0][0] ?? "";
    }

    public function getValueCells(string $nameList, string $range): array
    {
        try
        {
            $response = $this->service->spreadsheets_values
                ->get($this->spreadsheetId, $nameList . '!' . $range)
                ->values;

            if (empty($response)) return [];
            return $response;
        } catch (\Exception $ex)
        {
            echo "ERROR" . __FILE__ . " " . __LINE__ . " " . $ex->getMessage() . " " . $ex->getFile() . " " . $ex->getLine();
        }
        return [];
    }

    public function appendValues(string $sheetName, array $valuesArray, string $range, bool $toLastRow = false): GoogleSheetsService
    {
        $rowInfo = $this->getRowInfoByRange($range);
        if ($toLastRow)
        {
            $this->appendLastRow($valuesArray, $sheetName, $range,);
            return $this;
        }
        if (empty($rowInfo)) return $this;
        $this->insertRow($sheetName, $rowInfo['startRowIndex'], count($valuesArray));
        $this->insertValues($sheetName, $valuesArray, $range);

        return $this;
    }

    private function getRowInfoByRange(string $range): array|null
    {
        if (!str_contains($range, ":")) return null;
        $spl = explode(":", $range);
        if (count($spl) != 2)
            return null;

        $firstRow = preg_replace("/[^0-9]/", '', $spl[0]);
        $secondRow = preg_replace("/[^0-9]/", '', $spl[1]);

        if (!is_numeric($firstRow))
            return null;

        $startColumn = str_replace($firstRow, "", $spl[0]);
        $endColumn = str_replace($secondRow, "", $spl[1]);

        $startColumnIndex = $this->columnLetterToNumber($startColumn);
        $endColumnIndex = $this->columnLetterToNumber($endColumn);
        $rowInfo = [
            'startRowIndex'     => intval($firstRow) - 1,
            'endRowIndex'       => is_numeric($secondRow) ? intval($secondRow) : intval($firstRow) - 1,
            'startColumnIndex'  => $startColumnIndex - 1,
            'endColumnIndex'    => $endColumnIndex - 1,
            'startColumnLetter' => $startColumn,
            'endColumnLetter'   => $endColumn,
        ];
        return $rowInfo;
    }

    private function columnLetterToNumber(string $columnLetter): int
    {
        $columnLetter = strtoupper($columnLetter);
        $number = 0;
        for ($i = 0; $i < strlen($columnLetter); $i++)
        {
            $number = $number * 26 + (ord($columnLetter[$i]) - 64);
        }
        return $number;
    }

    public function appendLastRow(array $values, string $nameList, string $range, string $type = "USER_ENTERED"): void
    {
        $body = new ValueRange(['values' => $values]);
        $options = array('valueInputOption' => $type);
        try
        {
            $this->service->spreadsheets_values->append($this->spreadsheetId, $nameList . '!' . $range, $body, $options);
        } catch (Exception $e)
        {
            echo "ERROR" . __FILE__ . " " . __LINE__ . " " . $e->getMessage();
        }
    }

    public function insertRow(string $sheetName, int $rowStart, int $rowLength): GoogleSheetsService
    {
        $sheetId = $this->getSheetIdByName($this->spreadsheetId, $sheetName);
        // Создаем запросы на вставку пустых строк и добавление значений
        $request =
            new Google_Service_Sheets_Request([
                'insertRange' => [
                    'range'          => [
                        'sheetId'       => $sheetId,
                        'startRowIndex' => $rowStart,
                        'endRowIndex'   => $rowStart + $rowLength,
                    ],
                    'shiftDimension' => 'ROWS',
                ],
            ]);
        $this->temp_requests[] = $request;
        return $this;
    }

    private function getSheetIdByName($spreadsheetId, $sheetName): string|int
    {
        if (isset($this->sheets_id[$sheetName]))
            return $this->sheets_id[$sheetName];
        $spreadsheet = $this->service->spreadsheets->get($spreadsheetId);
        foreach ($spreadsheet->getSheets() as $sheet)
        {
            if ($sheet->getProperties()->getTitle() === $sheetName)
            {
                $sh_id = $sheet->getProperties()->getSheetId();
                $this->sheets_id[$sheetName] = $sh_id;
                return $sh_id;
            }
        }
        throw new \Exception("Sheet not found: " . $sheetName);
    }

    public function insertValues(string $sheetName, array $valuesArray, string $range, bool $onlyString = false): GoogleSheetsService
    {
        $rowInfo = $this->getRowInfoByRange($range);
        $sheetId = $this->getSheetIdByName($this->spreadsheetId, $sheetName);

        $request = new Google_Service_Sheets_Request([
            'updateCells' => [
                'range'  => [
                    'sheetId'          => $sheetId,
                    'startRowIndex'    => $rowInfo['startRowIndex'],
                    'startColumnIndex' => $rowInfo['startColumnIndex'],
                    'endRowIndex'      => $rowInfo['startRowIndex'] + count($valuesArray),
                ],
                'rows'   => array_map(function ($values) use ($onlyString) {
                    return new Google_Service_Sheets_RowData([
                        'values' => array_map(function ($value) use ($onlyString) {
                            $extendedValue = new Google_Service_Sheets_ExtendedValue();
                            if (is_numeric($value) && !$onlyString)
                                $extendedValue->setNumberValue($value);
                            else if (is_bool($value) && !$onlyString)
                                $extendedValue->setBoolValue($value);
                            else
                            {
                                $str = (string)$value;
                                if (mb_strlen($str) > 2 && mb_substr($str, 0, 2) == "==")
                                    $extendedValue->setFormulaValue(mb_substr($str, 1, mb_strlen($str) - 1));
                                else
                                    $extendedValue->setStringValue($str);
                            }
                            return new Google_Service_Sheets_CellData([
                                'userEnteredValue' => $extendedValue,
                            ]);
                        }, $values),
                    ]);
                }, $valuesArray),
                'fields' => 'userEnteredValue',
            ],
        ]);
        $this->temp_requests[] = $request;
        return $this;
    }

    public function execute(): void
    {
        if (empty($this->temp_requests)) return;

        $batchUpdateRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
            'requests' => $this->temp_requests,
        ]);

        $this->temp_requests = [];
        $this->service->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdateRequest);
    }

    public function createSheet($sheetName): GoogleSheetsService
    {
        $addSheet = new Google_Service_Sheets_Request([
            'addSheet' => [
                'properties' => [
                    'title' => $sheetName
                ]
            ]
        ]);
        $this->temp_requests[] = $addSheet;
        return $this;
    }

    public function duplicateSheet(string $copySheetName, string $newSheetName): GoogleSheetsService
    {
        $sheetId = $this->getSheetIdByName($this->spreadsheetId, $copySheetName);

        $request = new Google_Service_Sheets_Request([
            'duplicateSheet' => [
                'sourceSheetId' => $sheetId,
                'newSheetName'  => $newSheetName
            ]
        ]);

        $this->temp_requests[] = $request;
        return $this;
    }

    public function setFont(string $sheetName, string $range, string $fontFamily = null, int $fontSize = null): GoogleSheetsService
    {
        // Получаем идентификатор листа по его названию
        $sheetId = $this->getSheetIdByName($this->spreadsheetId, $sheetName);
        $rowInfo = $this->getRowInfoByRange($range);
        // Подготавливаем запрос на изменение формата ячеек

        $textFormat = new Google_Service_Sheets_TextFormat();
        if ($fontFamily) $textFormat->setFontFamily($fontFamily);
        if ($fontSize) $textFormat->setFontSize($fontSize);

        // Создаем объект для формата ячеек
        $cellFormat = new Google_Service_Sheets_CellFormat();
        $cellFormat->setTextFormat($textFormat);

        // Создаем объект для ячеек
        $cellData = new Google_Service_Sheets_CellData();
        $cellData->setUserEnteredFormat($cellFormat);

        // Подготавливаем запрос на изменение формата ячеек
        $request =
            new Google_Service_Sheets_Request([
                'repeatCell' => [
                    'range'  => [
                        'sheetId'          => $sheetId,
                        'startRowIndex'    => $rowInfo['startRowIndex'],
                        'endRowIndex'      => $rowInfo['endRowIndex'],
                        'startColumnIndex' => $rowInfo['startColumnIndex'],
                        'endColumnIndex'   => $rowInfo['endColumnIndex'] + 1,
                    ],
                    'cell'   => $cellData,
                    'fields' => 'userEnteredFormat.textFormat(fontFamily,fontSize)',
                ],
            ]);

        $this->temp_requests[] = $request;
        return $this;
    }

    public function setFormat(string $sheetName, string $range, $isBold = null,
                                     $isUnderline = null, $isItalic = null, $isStrike = null
    ): GoogleSheetsService
    {
        // Получаем идентификатор листа по его названию
        $sheetId = $this->getSheetIdByName($this->spreadsheetId, $sheetName);
        $rowInfo = $this->getRowInfoByRange($range);
        // Подготавливаем запрос на изменение формата ячеек

        $textFormat = new Google_Service_Sheets_TextFormat();
        if (!is_null($isBold)) $textFormat->setBold($isBold);
        if (!is_null($isUnderline)) $textFormat->setUnderline($isUnderline);
        if (!is_null($isItalic)) $textFormat->setItalic($isItalic);
        if (!is_null($isStrike)) $textFormat->setStrikethrough($isStrike);

        // Создаем объект для формата ячеек
        $cellFormat = new Google_Service_Sheets_CellFormat();
        $cellFormat->setTextFormat($textFormat);

        // Создаем объект для ячеек
        $cellData = new Google_Service_Sheets_CellData();
        $cellData->setUserEnteredFormat($cellFormat);

        // Подготавливаем запрос на изменение формата ячеек
        $request =
            new Google_Service_Sheets_Request([
                'repeatCell' => [
                    'range'  => [
                        'sheetId'          => $sheetId,
                        'startRowIndex'    => $rowInfo['startRowIndex'],
                        'endRowIndex'      => $rowInfo['endRowIndex'],
                        'startColumnIndex' => $rowInfo['startColumnIndex'],
                        'endColumnIndex'   => $rowInfo['endColumnIndex'] + 1,
                    ],
                    'cell'   => $cellData,
                    'fields' => 'userEnteredFormat.textFormat(bold,underline,italic,strikethrough)',
                ],
            ]);
        $this->temp_requests[] = $request;
        return $this;
    }

    public function insertColumn(string $sheetName, int $startIndex, int $lenColumns): GoogleSheetsService
    {
        $sheetId = $this->getSheetIdByName($this->spreadsheetId, $sheetName);
        $request = new Google_Service_Sheets_Request([
            'insertDimension' => [
                'range'             => [
                    'sheetId'    => $sheetId,
                    'dimension'  => 'COLUMNS',
                    'startIndex' => $startIndex,
                    'endIndex'   => $startIndex + $lenColumns,
                ],
                'inheritFromBefore' => false,
            ],
        ]);
        $this->temp_requests[] = $request;
        return $this;
    }

    //Ширина столбца
    public function setWidthColumn(string $sheetName, int $column_startIndex, int $size): GoogleSheetsService
    {
        $sheetId = $this->getSheetIdByName($this->spreadsheetId, $sheetName);

        $request = new Google_Service_Sheets_Request([
            'updateDimensionProperties' => [
                'range'      => [
                    'sheetId'    => $sheetId, // ID листа
                    'dimension'  => 'COLUMNS',
                    'startIndex' => $column_startIndex, // Начиная с третьего столбца
                    'endIndex'   => $column_startIndex + 1 // Для третьего столбца
                ],
                'properties' => [
                    'pixelSize' => $size // Ширина столбца (в пикселях), по умолчанию = 100px
                ],
                'fields'     => 'pixelSize'
            ]
        ]);
        $this->temp_requests[] = $request;
        return $this;
    }

    //Выравнивание
    public function setHorizontalAlignment(string $sheetName, string $range, $type = "CENTER"): GoogleSheetsService
    {
        $rowInfo = $this->getRowInfoByRange($range);
        $sheetId = $this->getSheetIdByName($this->spreadsheetId, $sheetName);

        $request = new Google_Service_Sheets_Request([
            'repeatCell' =>
                [
                    'range'  => [
                        'sheetId'          => $sheetId, // ID листа
                        'startRowIndex'    => 0, // Начиная с первой строки
                        'endRowIndex'      => 1000, // Для всех строк (вы можете изменить это на максимальное количество строк в вашей таблице)
                        'startColumnIndex' => $rowInfo['startColumnIndex'],
                        'endColumnIndex'   => $rowInfo['startColumnIndex'] + 1
                    ],
                    'cell'   => [
                        'userEnteredFormat' => [
                            'horizontalAlignment' => $type
                        ]
                    ],
                    'fields' => 'userEnteredFormat(horizontalAlignment)'
                ]
        ]);
        $this->temp_requests[] = $request;
        return $this;
    }

    public function setAlignment(string $sheetName, string $range, $verticalAlignment = "MIDDLE", $horizontalAlignment = "CENTER"): GoogleSheetsService
    {
        $rowInfo = $this->getRowInfoByRange($range);
        $sheetId = $this->getSheetIdByName($this->spreadsheetId, $sheetName);

        $request = new Google_Service_Sheets_Request([
            'repeatCell' => [
                'range'  => [
                    'sheetId'          => $sheetId,
                    'startRowIndex'    => $rowInfo['startRowIndex'],
                    'endRowIndex'      => $rowInfo['endRowIndex'],
                    'startColumnIndex' => $rowInfo['startColumnIndex'],
                    'endColumnIndex'   => $rowInfo['endColumnIndex']
                ],
                'cell'   => [
                    'userEnteredFormat' => [
                        'verticalAlignment'   => $verticalAlignment,
                        'horizontalAlignment' => $horizontalAlignment
                    ]
                ],
                'fields' => 'userEnteredFormat(verticalAlignment,horizontalAlignment)'
            ]
        ]);

        $this->temp_requests[] = $request;
        return $this;
    }


    private function columnNumberToLetter(string $columnNumber): string
    {
        $letter = '';
        while ($columnNumber > 0)
        {
            $remainder = ($columnNumber - 1) % 26;
            $letter = chr(65 + $remainder) . $letter;
            $columnNumber = ($columnNumber - $remainder - 1) / 26;
        }
        return $letter;
    }

    public function updateValueCell(string $value, string $nameList, string $range, string $inputOption = "USER_ENTERED"): void
    {
        try
        {
            $body = new \Google\Service\Sheets\ValueRange(['values' => [[$value]]]);
            $options = array('valueInputOption' => $inputOption);
            $this->service->spreadsheets_values->update($this->spreadsheetId, $nameList . '!' . $range, $body, $options);
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
    }

    public function updateValueCells(array $values, string $nameList, string $range, string $inputOption = "USER_ENTERED"): void
    {
        try
        {
            if (!str_contains($range, ":"))
            {
                if (is_array($values[0]))
                    $this->updateValueCell($values[0][0], $nameList, $range);
                else
                    $this->updateValueCell($values[0], $nameList, $range);
                return;
            }
            $body = new \Google\Service\Sheets\ValueRange(['values' => [$values]]);
            $options = array('valueInputOption' => $inputOption);
            $this->service->spreadsheets_values->update($this->spreadsheetId, $nameList . '!' . $range, $body, $options);
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
    }
}
