<?php declare(strict_types=1);

namespace App\Domains\CvedixrtInstance\Controller;

use Illuminate\Support\Facades\Route;

Route::group([], static function () {
    Route::get('/cvedixrt_instance', IndexController::class)->name('cvedixrt_instance.index');

    Route::any('/cvedixrt_instance/create', CreateController::class)->name('cvedixrt_instance.create');

    Route::any('/cvedixrt_instance/{id}', UpdateController::class)->name('cvedixrt_instance.update');
});
