<?php

namespace App\Console\Commands;

use App\Telegram\Methods\Bot\SetMyCommands;
use App\Telegram\TelegramBot;
use Illuminate\Console\Command;

class createCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создание кнопок-комманд в боте';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try
        {
            $bot = new TelegramBot();

            $setCommand = new SetMyCommands();
            $setCommand->commands = [
                ['command' => '/start', 'description' => 'Перезапустить бота']
            ];
            $setCommand->scope = ['type' => 'all_private_chats'];
            $setCommand->language_code = 'ru';

            $info = $bot->execute($setCommand);

            echo $info->isError ? "failed" : "success";
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }

    }
}
