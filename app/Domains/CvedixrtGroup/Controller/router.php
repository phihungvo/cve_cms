<?php declare(strict_types=1);

namespace App\Domains\CvedixrtGroup\Controller;

use Illuminate\Support\Facades\Route;

Route::group([], static function () {
    Route::get('/cvedixrt_group', IndexController::class)->name('cvedixrt_group.index');

    Route::any('/cvedixrt_group/create', CreateController::class)->name('cvedixrt_group.create');

    Route::any('/cvedixrt_group/{id}', UpdateController::class)->name('cvedixrt_group.update');
});
