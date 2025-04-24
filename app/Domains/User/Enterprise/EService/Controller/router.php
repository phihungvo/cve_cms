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


    Route::post('/user/enterprise/service/{id}/delete', EServiceDelete::class)->name('user.enterprise.eservice.delete');

});