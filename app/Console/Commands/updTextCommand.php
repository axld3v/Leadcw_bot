<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class updTextCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:upd-text';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновить текста';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try
        {
            \App\Text\GoogleSheetsText\TextSheets::handle();
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }

    }
}
