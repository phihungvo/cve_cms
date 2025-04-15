<?php

declare(strict_types=1);

namespace App\Domains\Campaign\Controller;

use Illuminate\Support\Facades\Route;
use App\Domains\Campaign\Controller\Index as CampaignIndex;
use App\Domains\Campaign\Controller\Create as CampaignCreate;

Route::prefix('campaign')->name('campaign.')->group(function () {
    Route::get('/', CampaignIndex::class)->name('index');
    Route::get('/create', [CampaignCreate::class, '__invoke'])->name('create');
    Route::post('/', [CampaignCreate::class, 'store'])->name('store');
    Route::get('/{id}/edit', [CampaignCreate::class, 'edit'])->name('edit');
    Route::put('/{id}', [CampaignCreate::class, 'update'])->name('update');
    Route::delete('/{id}', [CampaignCreate::class, 'destroy'])->name('destroy');
    Route::post('/{id}/restore', [CampaignCreate::class, 'restore'])->name('restore');
    Route::delete('/{id}/force', [CampaignCreate::class, 'forceDelete'])->name('forceDelete');
    Route::get('/media-by-enterprise/{enterprise_id}', [CampaignCreate::class, 'getMediaByEnterprise'])->name('media-by-enterprise');
    Route::get('/users-by-enterprise/{enterprise_id}', [CampaignCreate::class, 'getUsersByEnterprise'])->name('users-by-enterprise');
});
