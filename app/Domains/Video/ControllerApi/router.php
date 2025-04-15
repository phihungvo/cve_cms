<?php declare(strict_types=1);

namespace App\Domains\Video\ControllerApi;

use Illuminate\Support\Facades\Route;

Route::get('/video', Index::class)->name('video.index');