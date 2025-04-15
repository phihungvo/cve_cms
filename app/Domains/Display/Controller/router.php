<?php declare(strict_types=1);

namespace App\Domains\Device\ControllerApi;

use App\Domains\Display\Controller\ByPlaylistId;
use Illuminate\Support\Facades\Route;

Route::post('/display/by-playlist-id', ByPlaylistId::class)->name('display.by-playlist-id');
