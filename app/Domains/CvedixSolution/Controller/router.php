<?php declare(strict_types=1);

namespace App\Domains\CvedixSolution\Controller;

use Illuminate\Support\Facades\Route;

Route::group([], static function () {
    Route::get('/cvedix_solution', IndexController::class)->name('cvedix_solution.index');

    Route::any('/cvedix_solution/create', CreateController::class)->name('cvedix_solution.create');

    Route::any('/cvedix_solution/{id}', UpdateController::class)->name('cvedix_solution.update');
});
