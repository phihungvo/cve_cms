<?php

declare(strict_types=1);

namespace App\Domains\Campaign\Media\Controller;

use App\Domains\Campaign\Media\Controller\Index as Index;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['user-auth']], static function () {
    Route::get('/fpp/media', Index::class)->name('fpp.media.index');
    Route::get('/fpp/media/create', \App\Livewire\MediaUpload::class)->name('fpp.media.create');
    Route::post('/fpp/media/{id}/restore', [Index::class, 'restore'])->name('fpp.media.restore');
    Route::delete('/fpp/media/{id}/force-delete', [Index::class, 'forceDelete'])->name('fpp.media.force-delete');
    Route::delete('/fpp/media/delete', [Index::class, 'destroy'])->name('fpp.media.delete');
    Route::put('/fpp/media/rename', [Index::class, 'rename'])->name('fpp.media.rename');
});