<?php declare(strict_types=1);

namespace App\Domains\Display\ControllerApi;

use Illuminate\Support\Facades\Route;

Route::patch('/display/{id}', Update::class)->name('display.update');
