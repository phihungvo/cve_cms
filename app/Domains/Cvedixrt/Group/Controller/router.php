<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Group\Controller;

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['user-auth']], static function () {
    Route::get('/cvedixrt/group', IndexController::class)->name('cvedixrt_group.index');

    Route::any('/cvedixrt/group/create', CreateController::class)->name('cvedixrt_group.create');

    Route::any('/cvedixrt/group/{id}', UpdateController::class)->name('cvedixrt_group.update');
});
