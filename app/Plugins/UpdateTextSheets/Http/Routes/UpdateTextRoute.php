<?php

use Illuminate\Support\Facades\Route;

Route::get('/updText', [\App\Plugins\UpdateTextSheets\Http\Controller\UpdateTextController::class, 'updText'])->name('cron.updText');
