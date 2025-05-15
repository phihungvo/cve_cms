<?php declare(strict_types=1);

namespace App\Domains\CvedixGroup\Controller;

use Illuminate\Support\Facades\Route;

Route::group([], static function () {
    Route::get('/cvedix_group', IndexController::class)->name('cvedix_group.index');

    Route::any('/cvedix_group/create', CreateController::class)->name('cvedix_group.create');

    Route::any('/cvedix_group/{id}', UpdateController::class)->name('cvedix_group.update');
});
