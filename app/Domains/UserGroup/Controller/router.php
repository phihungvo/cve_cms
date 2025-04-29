<?php declare(strict_types=1);

namespace App\Domains\UserGroup\Controller;

use Illuminate\Support\Facades\Route;

Route::group([], static function () {
    Route::get('/user/group', IndexController::class)->name('group.index');

    Route::any('/user/group/create', CreateController::class)->name('group.create');

    Route::any('/user/group/{id}', UpdateController::class)->name('group.update');
});
