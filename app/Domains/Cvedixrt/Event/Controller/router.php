<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Event\Controller;

use Illuminate\Support\Facades\Route;

Route::group([], static function () {
    Route::get('/cvedixrt/event', IndexController::class)->name('cvedixrt_event.index');
});
