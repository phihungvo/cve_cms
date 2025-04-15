<?php declare(strict_types=1);

namespace App\Domains\CamCloud\Controller;

use Illuminate\Support\Facades\Route;

Route::group([], static function () {
    Route::get('/cam-cloud', IndexController::class)->name('cam-cloud.index');
});
