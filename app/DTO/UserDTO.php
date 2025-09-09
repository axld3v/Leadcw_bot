<?php

namespace App\DTO;

use App\Models\User;
use App\Services\GoogleSheetsService;
use App\Telegram\TelegramBot;
use App\Telegram\Types\TelegramRequest\TelegramMethods;
use App\Telegram\Types\TelegramRequest\TelegramResult;
use App\Telegram\Types\Update\UpdateType;

class UserDTO
{
    public ?User $user;
    public ?UpdateType $update;
    public ?TelegramBot $bot;
    private ?GoogleSheetsService $sheets = null;

    public function __construct(User $user, TelegramBot $telegramBot, UpdateType $update = null, GoogleSheetsService $sheets = null)
    {
        $this->user = $user;
        $this->bot = $telegramBot;
        if (!is_null($update)) $this->update = $update;
        if (!is_null($sheets)) $this->sheets = $sheets;
    }

    public function getSheets($sheets_id = ""): ?GoogleSheetsService
    {
        if (is_null($this->sheets))
            $this->sheets = new GoogleSheetsService($sheets_id);
        return $this->sheets;
    }

    public function execute(TelegramMethods $methods): TelegramResult|null
    {
        if (!isset($this->bot)) return null;
        try
        {
            return $this->bot->execute($methods);
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return null;
    }
}
