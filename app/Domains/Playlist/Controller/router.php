<?php declare(strict_types=1);

namespace App\Domains\Playlist\Controller;

use App\Domains\CamCloud\Controller\Index;
use Illuminate\Support\Facades\Route;

Route::group([], static function () {
    Route::get('/fpp/playlist', IndexController::class)->name('fpp.playlist.index');

    Route::any('/fpp/playlist/create', CreateController::class)->name('fpp.playlist.create');

    Route::any('/fpp/playlist/{id}', UpdateController::class)->name('fpp.playlist.update');

    // Tạo route nhận request push message
    Route::post('/fpp/playlist/push-message-all-devices', PushMessageController::class)
        ->name('fpp.playlist.push-message');
    Route::post('/fpp/playlist/push-message-to-devices', PushMessageToDevicesController::class)
        ->name('fpp.playlist.push-message-to-devices');
    Route::post('/fpp/playlist/preview-message', PreviewMessageController::class)
        ->name('playlist.preview-message');
});
