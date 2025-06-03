<?php

declare(strict_types=1);

namespace App\Domains\Notification\Controller;

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['user-auth']], static function () {
    Route::get('/notification', Index::class)->name('notification.index');
    Route::match(['get', 'post'], '/notification/create', Create::class)->name('notification.create');
    Route::match(['get', 'patch'], '/notification/{id}/update', Update::class)->name('notification.update');
    Route::delete('/notification/{id}/delete', [Index::class, 'destroy'])->name('notification.delete');
    Route::post('/notification/{id}/restore', [Index::class, 'restore'])->name('notification.restore');
    Route::delete('/notification/{id}/force-delete', [Index::class, 'forceDelete'])->name('notification.force-delete');

    Route::patch('/notification/{id}/read', [Index::class, 'markAsRead'])->name('notification.read');
    Route::get('/notification/{id}', [Index::class, 'show'])->name('notification.show');
    Route::get('/notification/users-by-enterprise', UserByEnterpriseController::class)->name('notification.users-by-enterprise');
    Route::get('/notification/roles-by-enterprise', RolesByEnterpriseController::class)->name('notification.roles-by-enterprise');

    Route::post('/notification/push-message-all-devices', PushMessageController::class)->name('notification.push-message');
    Route::post('/notification/push-message-to-devices', PushMessageToDevicesController::class)->name('notification.push-message-to-devices');
    Route::get('/notification/{id}/device-status', [Index::class, 'deviceStatus'])->name('notification.device-status');
    Route::get('/notification/unread-count', [Index::class, 'unreadCount'])->name('notification.unread-count');
});