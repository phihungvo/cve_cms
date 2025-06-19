<?php

declare(strict_types=1);

namespace App\Domains\CvedixtModel\Controller;

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['user-auth']], static function () {
    Route::get('/cvedixrt-model/index', [Index::class, '__invoke'])->name('cvedixrt_model.index');
    Route::post('/cvedixrt-model/create', Create::class)->name('cvedixrt_model.create');
    Route::post('/cvedixrt-model/create-folder', [Index::class, 'createFolder'])->name('cvedixrt_model.create-folder');
    Route::post('/cvedixrt-model/{id}/restore', [Index::class, 'restore'])->name('cvedixrt_model.restore');
    Route::delete('/cvedixrt-model/{id}/force-delete', [Index::class, 'forceDelete'])->name('cvedixrt_model.force-delete');
    Route::delete('/cvedixrt-model/delete', [Index::class, 'destroy'])->name('cvedixrt_model.delete');
    Route::put('/cvedixrt-model/rename', [Index::class, 'rename'])->name('cvedixrt_model.rename');
    Route::get('/cvedixrt-model/{id}/download', [Index::class, 'download'])->name('cvedixrt_model.download');
    Route::get('/cvedixrt-model/folder/{path}', [Index::class, 'getFolderContents'])->name('cvedixrt_model.folder.contents');
});
