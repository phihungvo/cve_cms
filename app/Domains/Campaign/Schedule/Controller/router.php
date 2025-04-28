<?php

declare(strict_types=1);

namespace App\Domains\Campaign\Schedule\Controller;

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['user-auth']], static function () {
    Route::get('/fpp/schedule', Index::class)->name('schedule.index');
    Route::match(['get', 'post'], '/fpp/schedule/create', Create::class)->name('schedule.create');
    Route::match(['get', 'patch'], '/fpp/schedule/{id}', Update::class)->name('schedule.update');
    Route::patch('/fpp/schedule/{id}/toggle', [Toggle::class, '__invoke'])->name('schedule.toggle');
    Route::patch('/fpp/schedule/{id}/restore', [Restore::class, '__invoke'])->name('schedule.restore');
    Route::delete('/fpp/schedule/delete', [Index::class, 'destroy'])->name('schedule.delete');

    Route::post('/fpp/schedule/push-message', PushMessage::class)->name('schedule.push-message');
    Route::post('/fpp/schedule/push-message-to-devices', PushMessageToDevices::class)
        ->name('schedule.push-message-to-devices');
    Route::post('/fpp/schedule/preview-message', PreviewMessage::class)
        ->name('schedule.preview-message');
});
