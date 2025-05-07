<?php declare(strict_types=1);

namespace App\Domains\VehicleGroup\Controller;

use Illuminate\Support\Facades\Route;

Route::group([], static function () {
    Route::get('/vehicle_group', IndexController::class)->name('vehicle_group.index');

    Route::any('/vehicle_group/create', CreateController::class)->name('vehicle_group.create');

    Route::any('/vehicle_group/{id}', UpdateController::class)->name('vehicle_group.update');
});
