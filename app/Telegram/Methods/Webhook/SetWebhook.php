<?php

namespace App\Telegram\Methods\Webhook;

use App\Telegram\Types\TelegramRequest\TelegramMethods;
use App\Telegram\Types\TelegramRequest\TelegramResult;

class SetWebhook extends TelegramMethods
{
    public string $methodName = "SetWebhook";

    /**
     * Required
     * HTTPS URL to send updates to. Use an empty string to remove webhook integration
     * @var string
     */
    public string $url = '';

    /**
     * The fixed IP address which will be used to send webhook requests instead of the IP address resolved through DNS
     * @var string
     */
    public string $ip_address = '';
    /**
     *
     * The maximum allowed number of simultaneous HTTPS connections to the webhook for update delivery,
     * 1-100. Defaults to 40. Use lower values to limit the load on your bot's server, and higher values to increase your bot's throughput.
     * @var int
     */
    public int $max_connections = 40;

    /**
     * A JSON-serialized list of the update types you want your bot to receive.
     * For example, specify ["message", "edited_channel_post", "callback_query"]
     * @var array
     */
    public array $allowed_updates = [];

    /**
     * Pass True to drop all pending updates
     * @var bool
     */
    public bool $drop_pending_updates = true;

    /**
     * A secret token to be sent in a header “X-Telegram-Bot-Api-Secret-Token” in every webhook request, 1-256 characters
     * @var string
     */
    public string $secret_token = "";

    static function result(TelegramResult $result): null|array
    {
        $jsonResponse = $result->response->decodedData ?? null;
        $request = $result->response->request ?? null;
        if (!is_array($jsonResponse) || empty($jsonResponse)) return null;
        $info = "";
        $status = isset($jsonResponse['result']) && $jsonResponse['result'] === true
            ? "success"
            : "error";

        if (isset($jsonResponse['description']))
        {
            $site = $request->fieldsArray['url'] ?? "";
            $info = $jsonResponse['description'] . "\nsite: " . $site;
        }
        return ['info' => $info, 'status' => $status];
    }
}
