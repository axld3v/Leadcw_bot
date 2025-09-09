<?php

namespace App\Telegram\Types\TelegramRequest;

class TelegramResponse
{
    public mixed $resultData = [];
    public string $errorInfo = "";
    public array $decodedData = [];
    public TelegramRequest $request;
    private string $rawData = '';

    public function __construct(string $rawData, TelegramRequest $request)
    {
        $this->request = $request;
        $this->fillRawData($rawData);
    }

    /**
     * Fills in the raw data
     *
     * @param string $rawData
     * @return TelegramResponse
     */
    public function fillRawData(string $rawData): TelegramResponse
    {
        try
        {
            $this->rawData = $rawData;
            $this->decodedData =
                json_decode($this->rawData, true) ?? [];
            if (!isset($this->decodedData['ok']) || $this->decodedData['ok'] === false)
                $this->errorInfo = $this->decodedData['description'] ?? "Unknown error";
            else
                $this->resultData = $this->decodedData['result'] ?? [];
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return $this;
    }

    public function getResult(): TelegramResult
    {
        return new TelegramResult($this);
    }

    /**
     * @return string
     */
    public function getRawData(): string
    {
        return $this->rawData;
    }
}
