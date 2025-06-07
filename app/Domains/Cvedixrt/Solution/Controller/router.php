<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Solution\Controller;

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['user-auth']], static function () {
    Route::get('/cvedixrt/solution', IndexController::class)->name('cvedixrt_solution.index');

    Route::any('/cvedixrt/solution/create', CreateController::class)->name('cvedixrt_solution.create');

    Route::any('/cvedixrt/solution/{id}', UpdateController::class)->name('cvedixrt_solution.update');
});
