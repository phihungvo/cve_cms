<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\EService\Controller;

use App\Domains\User\Enterprise\Billing\Controller\Index as BillingIndex;
use App\Domains\User\Enterprise\Billing\Controller\Create as BillingCreate;
use App\Domains\User\Enterprise\Billing\Controller\Update as BillingUpdate;
use App\Domains\User\Enterprise\Billing\Controller\Delete as BillingDelete;
use App\Domains\User\Enterprise\Billing\Controller\Restore as BillingRestore;


use Illuminate\Support\Facades\Route;

Route::middleware(['user-auth'])->group(function () {
    Route::get('/user/enterprise/billing', BillingIndex::class)
        // ->middleware('check.permission:read')
        ->name('user.enterprise.billing.index');

    Route::match(['get', 'post'], '/user/enterprise/billing/create', BillingCreate::class)->name('user.enterprise.billing.create');

    Route::match(['get', 'patch'], '/user/enterprise/billing/{id}', BillingUpdate::class)->name('user.enterprise.billing.update');


    Route::post('/user/enterprise/billing/{id}/delete', BillingDelete::class)->name('user.enterprise.billing.delete');

    Route::post('/user/enterprise/billing/{id}/restore', [BillingRestore::class, '__invoke'])->name('user.enterprise.billing.restore');
});