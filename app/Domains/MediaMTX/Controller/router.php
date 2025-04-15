<?php declare(strict_types=1);

use App\Domains\MediaMTX\Controller\Paths as MediaMTXController;
use Illuminate\Support\Facades\Route;

Route::group([], static function () {
    Route::get('monitor/mediamtx/paths', MediaMTXController::class)->name('monitor.mediamtx.paths');
});
