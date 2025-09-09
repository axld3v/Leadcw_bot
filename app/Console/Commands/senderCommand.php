<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class senderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sender';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Запустить рассылку';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try
        {
            \App\Plugins\Sender\Methods\SenderMethods::sender();
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }

    }
}
