<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\EService\Controller;

use App\Domains\User\Enterprise\License\Controller\Index as LicenseIndex;
use App\Domains\User\Enterprise\License\Controller\Create as LicenseCreate;
use App\Domains\User\Enterprise\License\Controller\Update as LicenseUpdate;
use App\Domains\User\Enterprise\License\Controller\Delete as LicenseDelete;
use App\Domains\User\Enterprise\License\Controller\Restore as LicenseRestore;


use Illuminate\Support\Facades\Route;

Route::middleware(['user-auth'])->group(function () {
    Route::get('/user/enterprise/license', LicenseIndex::class)
        // ->middleware('check.permission:read')
        ->name('user.enterprise.license.index');

    Route::match(['get', 'post'], '/user/enterprise/license/create', LicenseCreate::class)->name('user.enterprise.license.create');

    Route::match(['get', 'patch'], '/user/enterprise/license/{id}', LicenseUpdate::class)->name('user.enterprise.license.update');


    Route::post('/user/enterprise/license/{id}/delete', LicenseDelete::class)->name('user.enterprise.license.delete');

    Route::post('/user/enterprise/license/{id}/restore', [LicenseRestore::class, '__invoke'])->name('user.enterprise.license.restore');
});