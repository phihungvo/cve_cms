<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Instance\Controller;

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['user-auth']], static function () {
    Route::get('/cvedixrt/instance', IndexController::class)->name('cvedixrt_instance.index');

    Route::any('/cvedixrt/instance/create', CreateController::class)->name('cvedixrt_instance.create');

    Route::any('/cvedixrt/instance/{id}', UpdateController::class)->name('cvedixrt_instance.update');

    Route::any('/cvedixrt/instance/{id}/analytics', RTAnalyticsIndex::class)->name('cvedixrt_instance.analytics');

    // API routes for canvas shapes
    Route::get('/cvedixrt/instances/{id}/shapes', [CanvasController::class, 'getShapes'])->name('cvedixrt_instance.shapes.get');

    Route::post('/cvedixrt/instances/{id}/shapes', [CanvasController::class, 'saveShapes'])->name('cvedixrt_instance.shapes.save');
});
