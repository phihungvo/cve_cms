<?php declare(strict_types=1);

namespace App\Domains\Playlist\PlaylistGroup\Controller;

use Illuminate\Support\Facades\Route;

Route::group([], static function () {
    Route::get('/fpp/playlist_group', IndexController::class)->name('playlist_group.index');

    Route::any('/fpp/playlist_group/create', CreateController::class)->name('playlist_group.create');

    Route::any('/fpp/playlist_group/{id}', UpdateController::class)->name('playlist_group.update');
});
