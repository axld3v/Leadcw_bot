<?php

namespace App\Telegram\Utilities;

use App\Telegram\TelegramBot;
use App\Telegram\Types\TelegramRequest\TelegramMethods;
use App\Telegram\Types\TelegramRequest\TelegramRequest;
use App\Telegram\Types\TelegramRequest\TelegramResponse;
use App\Telegram\Types\TelegramRequest\TelegramResult;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class SendRequest
{

    public static function postRequestTelegram(TelegramBot $bot, TelegramMethods $method): TelegramResult
    {

        $request = new TelegramRequest($method);
        $client = new Client();

        try
        {
            $response = $client->post($bot->getUrlBot() . "/" . $request->methodName,
                [
                    'json' => $request->fieldsArray,
                ]);

            $responseRaw = $response->getBody()->getContents();
        } catch (GuzzleException $e)
        {
            $responseRaw = $e->getResponse()->getBody()->getContents() ?? "";
            if (!str_contains($responseRaw, "chat not found"))
                logging($e, ['method_name' => $request->methodName, 'fields' => $request->fieldsArray, 'error' => $responseRaw], warning: true);
        } catch (Exception $e)
        {
            $responseRaw = $e->getMessage() ?? "";
            logging($e, ['method_name' => $request->methodName, 'fields' => $request->fieldsArray, 'error' => $responseRaw], warning: true);
        } catch (\Throwable $throwable)
        {
            logging($throwable, ['method_name' => $request->methodName, 'fields' => $request->fieldsArray], warning: true);
        }
        $resp = (new TelegramResponse($responseRaw, $request));
        return $resp->getResult();
    }
}
