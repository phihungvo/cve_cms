<?php declare(strict_types=1);

namespace App\Domains\ScheduleGroup\Controller;

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['user-auth']], static function () {
    Route::get('/schedule_group', IndexController::class)->name('schedule_group.index');

    Route::any('/schedule_group/create', CreateController::class)->name('schedule_group.create');

    Route::any('/schedule_group/{id}', UpdateController::class)->name('schedule_group.update');
});
