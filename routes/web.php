<?php

use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/video/playlist/{playlist}', [VideoController::class,'playlist'])->name('video.playlist');

Route::get('/video/key/{key}', [VideoController::class,'key'])->name('video.key');

