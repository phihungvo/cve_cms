<?php declare(strict_types=1);

namespace App\Domains\UserGroup\Controller;

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['user-auth']], static function () {
    Route::get('/user/group', IndexController::class)->name('user_group.index');

    Route::any('/user/group/create', CreateController::class)->name('user_group.create');

    Route::any('/user/group/{id}', UpdateController::class)->name('user_group.update');
});
