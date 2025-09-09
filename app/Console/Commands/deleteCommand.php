<?php

namespace App\Console\Commands;

use App\Telegram\Methods\Bot\DeleteMyCommands;
use App\Telegram\Methods\Bot\SetMyCommands;
use App\Telegram\TelegramBot;
use Illuminate\Console\Command;

class deleteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Удаление кнопок-комманд в боте';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try
        {
            $bot = new TelegramBot();

            $delCommand = new DeleteMyCommands();
            $delCommand->scope = ['type' => 'all_private_chats'];
            $delCommand->language_code = 'ru';

            $info = $bot->execute($delCommand);

            echo $info->isError ? "failed" : "success";
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }

    }
}
