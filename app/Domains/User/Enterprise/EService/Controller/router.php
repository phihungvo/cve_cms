<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\EService\Controller;

use App\Domains\User\Enterprise\EService\Controller\Index as EServiceIndex;
use App\Domains\User\Enterprise\EService\Controller\Create as EServiceCreate;
use App\Domains\User\Enterprise\EService\Controller\Update as EServiceUpdate;
use App\Domains\User\Enterprise\EService\Controller\Delete as EServiceDelete;
use Illuminate\Support\Facades\Route;

Route::middleware(['user-auth'])->group(function () {
    Route::get('/user/enterprise/service', EServiceIndex::class)
        // ->middleware('check.permission:read')
        ->name('user.enterprise.eservice.index');

    Route::match(['get', 'post'], '/user/enterprise/service/create', EServiceCreate::class)->name('user.enterprise.eservice.create');

    Route::match(['get', 'patch'], '/user/enterprise/service/{id}', EServiceUpdate::class)->name('user.enterprise.eservice.update');


    // Route::match(['get', 'patch'], '/user/permission/{id}', PermissionUpdate::class)
    //     ->name('user.permission.update');

    // Route::delete('/user/permission/{id}', PermissionDelete::class)
    // ->name('user.permission.delete');



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