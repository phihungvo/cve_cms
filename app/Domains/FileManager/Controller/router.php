<?php

declare(strict_types=1);

namespace App\Domains\FileManager\Controller;

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['user-auth']], static function () {
    Route::get('/file-management', [IndexController::class, '__invoke'])->name('file_management.index');
    Route::post('/file-management/create', CreateController::class)->name('file_management.create');
    Route::post('/file-management/create-folder', CreateFolderController::class)->name('file_management.create-folder');
    Route::delete('/file-management/delete', [IndexController::class, 'destroy'])->name('file_management.delete');
    Route::get('/file-management/{id}/download', [IndexController::class, 'download'])->name('file_management.download');
    Route::get('/file-management/folder/{path?}', [IndexController::class, 'getFolderContents'])
        ->where('path', '.*')
        ->name('file_management.folder.contents');
});
