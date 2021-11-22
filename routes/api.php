<?php

use App\Http\Controllers\Auth\MeController;
use App\Http\Controllers\CashController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', MeController::class);

    Route::prefix('cash')->group(function () {
        Route::get('', [CashController::class, 'index']);
        Route::post('create', [CashController::class, 'store']);
    });
});
