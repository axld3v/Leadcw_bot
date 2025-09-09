<?php

namespace App\Http\Controllers;

use App\Telegram\Methods\Webhook\DeleteWebhook;
use App\Telegram\Methods\Webhook\GetWebhookInfo;
use App\Telegram\Methods\Webhook\SetWebhook;
use App\Telegram\Receive\ReceiveUpdates;
use App\Telegram\TelegramBot;
use App\Telegram\Utilities\ShowTextResult;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function set(Request $request)
    {
        try
        {
            $params_site = $request->query('webhook') ?? null;
            $bot = new TelegramBot();

            //create method
            $setWebhook = new SetWebhook();
            $setWebhook->url = is_null($params_site)
                ? route("webhook.receive")
                : $params_site;
            $setWebhook->url = str_replace("http://", "https://", $setWebhook->url);
            $setWebhook->drop_pending_updates = true;
            //execute method
            $resultSetWebhook = $bot->execute($setWebhook);

            //show result
            $infoSetWebhook = $setWebhook::result($resultSetWebhook);
            ShowTextResult::print($infoSetWebhook);

            //info Webhook
            $getWebhook = new getWebhookInfo();
            $resultInfoWebhook = $bot->execute($getWebhook);
            $infoGetWebhook = $getWebhook::result($resultInfoWebhook);
            ShowTextResult::print($infoGetWebhook, showTags: false);
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return response("")->header('Content-Type', 'application/json; charset=UTF-8');
    }

    public function delete()
    {
        try
        {
            $bot = new TelegramBot();
            //create method
            $getWebhook = new getWebhookInfo();
            $resultInfoWebhook = $bot->execute($getWebhook);
            $infoGetWebhook = $getWebhook::result($resultInfoWebhook);
            ShowTextResult::print($infoGetWebhook, showTags: false);

            $deleteWebhook = new DeleteWebhook();

            //execute method
            $result = $bot->execute($deleteWebhook);

            //show result
            $infoDeleteWebhook = $deleteWebhook::result($result);
            ShowTextResult::print($infoDeleteWebhook);
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return response("")->header('Content-Type', 'application/json; charset=UTF-8');
    }

    public function receive(Request $request)
    {
        try
        {
            if (mb_strtolower(config("app.env")) == 'production')
            {
                ReceiveUpdates::receive($request->json()->all());
            }
            else
            {
                ReceiveUpdates::receive();
            }
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
        return response("")->header('Content-Type', 'application/json; charset=UTF-8');
    }
}
