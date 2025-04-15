<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\Controller;

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'user/enterprise', 'middleware' => ['user-auth']], static function () {
    Route::get('/', [EnterpriseController::class, 'index'])->name('user.enterprise.index');
    Route::get('/create', [EnterpriseController::class, 'create'])->name('user.enterprise.create');
    // create route handle save enterprise
    Route::post('/', [EnterpriseController::class, 'store'])->name('user.enterprise.store');
    // create route show page update
    Route::get('/{id}', [EnterpriseController::class, 'show'])->name('user.enterprise.show');
    // create route handle update
    Route::put('/update/{id}', [EnterpriseController::class, 'update'])->name('user.enterprise.update');
    // create route handle delete
    Route::delete('/{id}', [EnterpriseController::class, 'destroy'])->name('user.enterprise.destroy');
    // create route handle restore
    Route::patch('/enterprise/{id}/restore', [EnterpriseController::class, 'restore'])->name('user.enterprise.restore');
    // create route handle force delete
    Route::delete('/enterprise/{id}/force-delete', [EnterpriseController::class, 'forceDelete'])->name('user.enterprise.force-delete');
});
// create route handle export
Route::get('/publish', [TestController::class, 'index'])->name('user.enterprise.publish');
