<?php

declare(strict_types=1);

namespace App\Domains\CvedixtModel\Controller;

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['user-auth']], static function () {
    Route::get('/cvedixt-model/index', Index::class)->name('cvedixt-model.index');
    Route::post('/cvedixt-model/create', Create::class)->name('cvedixt-model.create');
    Route::post('/cvedixt-model/{id}/restore', [Index::class, 'restore'])->name('cvedixt-model.restore');
    Route::delete('/cvedixt-model/{id}/force-delete', [Index::class, 'forceDelete'])->name('cvedixt-model.force-delete');
    Route::delete('/cvedixt-model/delete', [Index::class, 'destroy'])->name('cvedixt-model.delete');
    Route::put('/cvedixt-model/rename', [Index::class, 'rename'])->name('cvedixt-model.rename');
});
