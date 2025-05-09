<?php declare(strict_types=1);

namespace App\Domains\PlaylistGroup\Controller;

use Illuminate\Support\Facades\Route;

Route::group([], static function () {
    Route::get('/playlist_group', IndexController::class)->name('playlist_group.index');

    Route::any('/playlist_group/create', CreateController::class)->name('playlist_group.create');

    Route::any('/playlist_group/{id}', UpdateController::class)->name('playlist_group.update');
});
