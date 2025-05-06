<?php declare(strict_types=1);

namespace App\Domains\UserGroup\Controller;

use Illuminate\Support\Facades\Route;

Route::group([], static function () {
    Route::get('/user/group', IndexController::class)->name('user-group.index');

    Route::any('/user/group/create', CreateController::class)->name('user-group.create');

    Route::any('/user/group/{id}', UpdateController::class)->name('user-group.update');
});
