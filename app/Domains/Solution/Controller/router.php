<?php declare(strict_types=1);

namespace App\Domains\Device\Controller;

use App\Domains\Solution\Controller\CreateController;
use App\Domains\Solution\Controller\IndexController;
use App\Domains\Solution\Controller\UpdateController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['user-auth']], static function () {
    /*
     * ===================================================
     * =        CVEDIX-RT-Analytics-Solution             =
     * ===================================================
     */
    Route::get('/device-cvedixrt-solution', IndexController::class)->name('solution.index');
    Route::any('/device-cvedixrt-solution/create', CreateController::class)->name('solution.create');
    Route::any('/device-cvedixrt-solution/{id}', UpdateController::class)->name('solution.update');
});
