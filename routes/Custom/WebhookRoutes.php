<?php

use Illuminate\Support\Facades\Route;

Route::get('/setWebhook', [\App\Http\Controllers\WebhookController::class, 'set'])->name('webhook.set');
Route::get('/deleteWebhook', [\App\Http\Controllers\WebhookController::class, 'delete'])->name('webhook.delete');
Route::post('/webhook', [\App\Http\Controllers\WebhookController::class, 'receive'])->name('webhook.receive');
