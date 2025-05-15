<?php declare(strict_types=1);

namespace App\Domains\CvedixrtSolution\Controller;

use Illuminate\Support\Facades\Route;

Route::group([], static function () {
    Route::get('/cvedixrt_solution', IndexController::class)->name('cvedixrt_solution.index');

    Route::any('/cvedixrt_solution/create', CreateController::class)->name('cvedixrt_solution.create');

    Route::any('/cvedixrt_solution/{id}', UpdateController::class)->name('cvedixrt_solution.update');
});
