<?php

use App\Domains\Mail\Controllers\MailController;
use Illuminate\Support\Facades\Route;

Route::prefix('mail')->group(function () {
    Route::post('/send-notification', [MailController::class, 'sendNotification']);
});