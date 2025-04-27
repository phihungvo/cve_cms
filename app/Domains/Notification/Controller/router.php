<?php

declare(strict_types=1);

namespace App\Domains\Notification\Controller;

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['user-auth']], static function () {
    Route::get('/notifications', Index::class)->name('notification.index');
    Route::post('/notifications/create', Create::class)->name('notification.create');
    Route::match(['get', 'patch'], '/notifications/{id}', Update::class)->name('notification.update');
    Route::delete('/notifications/delete', [Index::class, 'destroy'])->name('notification.delete');
    Route::post('/notifications/{id}/restore', [Index::class, 'restore'])->name('notification.restore');
    Route::delete('/notifications/{id}/force-delete', [Index::class, 'forceDelete'])->name('notification.force-delete');
});