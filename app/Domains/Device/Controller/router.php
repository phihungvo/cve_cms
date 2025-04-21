<?php declare(strict_types=1);

namespace App\Domains\Device\Controller;

use Illuminate\Support\Facades\Route;
use App\Domains\Device\Controller\CameraSetting;

Route::group(['middleware' => ['user-auth']], static function () {
    Route::get('/device', Index::class)->name('device.index');
    Route::any('/device/create', Create::class)->name('device.create');
    Route::any('/device/map', Map::class)->name('device.map');
    Route::any('/device/{id}', Update::class)->name('device.update');
    Route::any('/device/{id}/boolean/{column}', UpdateBoolean::class)->name('device.update.boolean');
    Route::any('/device/{id}/device-message', UpdateDeviceMessage::class)->name('device.update.device-message');
    Route::any('/device/{id}/device-message/create', UpdateDeviceMessageCreate::class)->name('device.update.device-message.create');
    Route::any('/device/{id}/device-message/{device_message_id}', UpdateDeviceMessageUpdate::class)->name('device.update.device-message.update');
    Route::any('/device/{id}/device-status', UpdateDeviceStatus::class)->name('device.update.device-status');
    Route::any('/device/{id}/device-log', UpdateDeviceLog::class)->name('device.update.device-log');

    Route::get('/device/{id}/camera-setting', [CameraSetting::class, '__invoke'])->name('device.update.camera-setting');
    Route::patch('/device/{id}/camera-setting', [CameraSetting::class, '__invoke'])->name('device.update.camera-setting.update');
    Route::post('/device/{id}/camera-setting/create', [CameraSetting::class, '__invoke'])->name('device.camera-setting.create');
    Route::delete('/device/{id}/camera-setting/{camera_id}', [CameraSetting::class, '__invoke'])->name('device.update.camera-setting.delete');

    Route::get('/device/{id}/runtime-analytics', RTAnalyticsIndex::class)->name('device.runtime-analytics');
    Route::any('/device/{id}/runtime-analytics/create', RTAnalyticsCreate::class)->name('device.runtime-analytics.create');
});

Route::group(['middleware' => ['user-auth-manager-mode']], static function () {
    Route::any('/device/{id}/transfer', UpdateTransfer::class)->name('device.update.transfer');
});
