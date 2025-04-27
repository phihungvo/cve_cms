<?php declare(strict_types=1);

namespace App\Domains\Device\Controller;

use App\Domains\Group\Controller\CreateController;
use App\Domains\Group\Controller\IndexController;
use App\Domains\Group\Controller\UpdateController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['user-auth']], static function () {
    /*
     * ===================================================
     * =        CVEDIX-RT-Analytics-Group                =
     * ===================================================
     */

    Route::get('/device-cvedixrt-group', IndexController::class)->name('group.index');
    Route::any('/device-cvedixrt-group/create', CreateController::class)->name('group.create');
    Route::any('/device-cvedixrt-group/{id}', UpdateController::class)->name('group.update');

});
