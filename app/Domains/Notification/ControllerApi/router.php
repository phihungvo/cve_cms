<?php

declare(strict_types=1);

namespace App\Domains\Notification\ControllerApi;

use App\Domains\Notification\Service\ControllerApi\NotificationList as NotificationListService;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::group(['middleware' => ['user-auth']], static function () {
    Route::get('/notification/get', GetNotification::class)->name('notification.get');
    Route::get('/notifications/read', NotificationMarkAsRead::class)->name('notification.mark-as-read');
});