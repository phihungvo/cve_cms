<?php declare(strict_types=1);

namespace App\Domains\DeviceGroup\Controller;

use Illuminate\Support\Facades\Route;

Route::group([], static function () {
    Route::get('/device/group', IndexController::class)->name('device_group.index');

    Route::any('/device/group/create', CreateController::class)->name('device_group.create');

    Route::any('/device/group/{id}', UpdateController::class)->name('device_group.update');
});
