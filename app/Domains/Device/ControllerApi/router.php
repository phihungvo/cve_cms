<?php declare(strict_types=1);

namespace App\Domains\Device\ControllerApi;

use Illuminate\Support\Facades\Route;

Route::get('/device', Index::class)->name('device.index');
Route::post('/device/create', Create::class)->name('device.create');
Route::patch('/device/{id}', Update::class)->name('device.update');
Route::delete('/device/{id}', Delete::class)->name('device.delete');
Route::post('/device/log', DeviceLog::class)->name('device.log.create');
Route::post('/device/status', DeviceStatus::class);
Route::get('/device/status', GetDeviceStatusBySerial::class);
Route::get('/device/media/capture/{id}', [GetListMediaCapture::class, 'data']);

Route::get('device/{id}/cvedixrt/event', RTAnalyticsEvent::class)->name('device_cvedixrt_event.index');


