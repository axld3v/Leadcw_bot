<?php

namespace App\Console\Commands;

use App\Telegram\Methods\Webhook\SetWebhook;
use App\Telegram\TelegramBot;
use App\Telegram\Utilities\ShowTextResult;
use Illuminate\Console\Command;

class createWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-webhook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Webhook connect';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $url = route('webhook.receive');
        try
        {
            $bot = new TelegramBot();

            //create method
            $setWebhook = new SetWebhook();
            $setWebhook->url = $url;
            $setWebhook->url = str_replace("http://", "https://", $setWebhook->url);
            $setWebhook->drop_pending_updates = true;
            //execute method
            $resultSetWebhook = $bot->execute($setWebhook);

            //show result
            $infoSetWebhook = $setWebhook::result($resultSetWebhook);
            ShowTextResult::print($infoSetWebhook);

            echo "Update text = cron: /3 * * * * curl " . route('cron.updText');
            echo "\nSender = cron: * * * * * curl " . route('cron.senderMsg') . "\n\n";
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }

    }
}
