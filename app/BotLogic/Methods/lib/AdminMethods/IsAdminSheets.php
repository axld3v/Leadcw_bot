<?php

namespace App\BotLogic\Methods\lib\AdminMethods;

use App\DTO\UserDTO;

class IsAdminSheets
{
    public static function handle(UserDTO $userDTO, string $namelist = "Админы"): bool
    {
        $find = false;
        try
        {
            $sheets = $userDTO->getSheets();
            $username = $userDTO->user->username;
            $sheets_admin = $sheets->getValueCells($namelist, "A2:A");
            if (is_null($sheets_admin)) return false;
            for ($i = 0; $i < count($sheets_admin); $i++)
            {
                if (empty($sheets_admin[$i][0])) continue;
                $sheetsAdminItem = $sheets_admin[$i][0];
                if (str_contains($sheetsAdminItem, "https://t.me/")) $sheetsAdminItem =
                    str_replace(["https://t.me/", ""], "", $sheetsAdminItem);

                if (mb_strtolower(trim($sheetsAdminItem)) != mb_strtolower(trim($username))) continue;
                $find = true;
                break;
            }
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return $find;
    }
}
