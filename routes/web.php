<?php

use Illuminate\Support\Facades\Route;

set_time_limit(60);

// Подключение всех файлов из папки custom
foreach (glob(base_path('routes/Custom/*.php')) as $file)
{
    require $file;
}

// Подключение всех Плагинов
foreach (glob(base_path('app/Plugins/*/Http/Routes/*Route.php')) as $file)
{
    require $file;
}

Route::view('/', 'home.index')->name('home');
