<?php

namespace App\Plugins\UpdateTextSheets\Http\Controller;

use Illuminate\Http\Request;

class UpdateTextController extends \App\Http\Controllers\Controller
{
    /**
     * Automatic text update for the bot, recommended to update every 3 minutes
     * crontab: /3 * * * * curl "http://your-domain.com/public/updText"
     */
    public function updText(Request $request)
    {
        try
        {
            \App\Text\GoogleSheetsText\TextSheets::handle();
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return response("")->header('Content-Type', 'application/json; charset=UTF-8');
    }
}
