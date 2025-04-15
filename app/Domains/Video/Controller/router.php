<?php

declare(strict_types=1);

namespace App\Domains\Video\Controller;

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['user-auth']], function () {
    Route::get('/video', Index::class)->name('video.index');
    Route::get('/video/create', Create::class)->name('video.create');
    Route::post('/video', [Create::class, 'store'])->name('video.store');
    Route::get('/video/{id}/edit', [Create::class, 'edit'])->name('video.edit');
    Route::put('/video/{id}', [Create::class, 'update'])->name('video.update');
    Route::delete('/video/{id}', [Create::class, 'destroy'])->name('video.destroy');
});
