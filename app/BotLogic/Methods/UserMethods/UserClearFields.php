<?php

namespace App\BotLogic\Methods\UserMethods;

use App\Models\User;

class UserClearFields
{
    public static function handle(User $user): void
    {
        $user->updateAndSave([
            'lastmessage' => '',
            'json_info'   => null,
        ]);
    }
}
