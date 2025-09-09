<?php

namespace App\Telegram;

use App\Telegram\Methods\Bot\GetMe;
use App\Telegram\Types\TelegramRequest\TelegramMethods;
use App\Telegram\Types\TelegramRequest\TelegramResult;
use App\Telegram\Utilities\SendRequest;

class TelegramBot
{
    private string $token;
    private string $id_bot;
    private string $first_name;
    private string $username;


    public function __construct(string $token = "")
    {
        if (empty($token))
            $token = config('telegram.bot.token');
        $this->token = $token;
    }

    public function getUsername(): string
    {
        if (!isset($this->username))
            $this->getMe();

        return $this->username;
    }

    private function getMe(): void
    {
        $info = SendRequest::postRequestTelegram($this, new GetMe());
        if (isset($info->result["username"]))
        {
            $this->id_bot = $info->result["id"] ?? "";
            $this->first_name = $info->result["first_name"] ?? "";
            $this->username = $info->result["username"] ?? "";
        }
    }

    public function getFirstName(): string
    {
        if (!isset($this->first_name))
            $this->getMe();

        return $this->first_name;
    }

    public function getIdBot(): string
    {
        if (!isset($this->id_bot))
            $this->getMe();

        return $this->id_bot;
    }

    public function execute(TelegramMethods $methods, TelegramMethods|null $tryIfError = null): TelegramResult
    {
        $rez = SendRequest::postRequestTelegram($this, $methods);
        if ($rez->isError && !(is_null($tryIfError))
            && $rez->error != "Bad Request: message thread not found")
            $rez = SendRequest::postRequestTelegram($this, $tryIfError);
        return $rez;
    }

    public function getUrlBot(): string
    {
        return "https://api.telegram.org/bot{$this->token}";
    }
}
