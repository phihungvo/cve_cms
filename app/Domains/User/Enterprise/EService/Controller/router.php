<?php declare(strict_types=1);

namespace App\Domains\User\Permission\Controller;

use App\Domains\User\Permission\Controller\Index as PermissionIndex;
use App\Domains\User\Permission\Controller\Create as PermissionCreate;
use App\Domains\User\Permission\Controller\Update as PermissionUpdate;
use App\Domains\User\Permission\Controller\Delete as PermissionDelete;
use Illuminate\Support\Facades\Route;

Route::middleware(['user-auth'])->group(function () {
    Route::get('/user/permission', PermissionIndex::class)
        // ->middleware('check.permission:read')
        ->name('user.permission.index');

    Route::any('/user/permission/create', PermissionCreate::class)
        // ->middleware('check.permission:create')
        ->name('user.permission.create');

    Route::match(['get', 'patch'], '/user/permission/{id}', PermissionUpdate::class)
        ->name('user.permission.update');

    Route::delete('/user/permission/{id}', PermissionDelete::class)
        ->name('user.permission.delete');



    // Route::get('/user/permission/role/{role_id}/edit', [PermissionUpdate::class, 'edit'])
    //     // ->middleware('check.permission:update')
    //     ->name('user.permission.edit');

    // Route::put('/user/permission/role/{role_id}/update', [PermissionUpdate::class, 'update'])
    //     // ->middleware('check.permission:update')
    //     ->name('user.permission.update');

    // Route::delete('/user/permission/{role_id}', [PermissionDelete::class, 'destroy'])
    //     // ->middleware('check.permission:delete')
    //     ->name('user.permission.destroy');

    // Route::patch('/user/permission/{role_id}/restore', [PermissionDelete::class, 'restore'])
    //     // ->middleware('check.permission:restore')
    //     ->name('user.permission.restore');

    // Route::delete('/user/permission/{role_id}/force-delete', [PermissionDelete::class, 'forceDelete'])
    //     // ->middleware('check.permission:force-delete')
    //     ->name('user.permission.force-delete');
});