<?php declare(strict_types=1);

namespace App\Domains\CvedixInstance\Controller;

use Illuminate\Support\Facades\Route;

Route::group([], static function () {
    Route::get('/cvedix_instance', IndexController::class)->name('cvedix_instance.index');

    Route::any('/cvedix_instance/create', CreateController::class)->name('cvedix_instance.create');

    Route::any('/cvedix_instance/{id}', UpdateController::class)->name('cvedix_instance.update');
});
