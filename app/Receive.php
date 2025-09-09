<?php

// Загрузите автозагрузчик Composer
use App\Telegram\Receive\ReceiveUpdates;

require __DIR__ . '/../vendor/autoload.php';

// Создайте экземпляр приложения Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Создайте экземпляр HTTP-запроса для приложения
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Создайте запрос для получения конфигурации
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle(
    $request
);

// Теперь вы можете использовать функции Laravel
// Например, получить конфигурацию
try
{
    //$response = json_decode(file_get_contents('php://input'), TRUE);
    //ReceiveUpdates::receive($response);
    ReceiveUpdates::receive();
    //\App\Text\GoogleSheetsText\TextSheets::handle();
} catch (\Throwable $throwable)
{
    logging($throwable);
    echo "error";
}
