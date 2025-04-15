<?php declare(strict_types=1);

namespace App\Domains\Position\ControllerApi;

use Illuminate\Support\Facades\Route;

Route::get('/position/heatmap', Heatmap::class)->name('position.heatmap');