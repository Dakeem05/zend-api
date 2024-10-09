<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TransactionController;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->group(function () {
    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('', 'auth');
    });

    Route::middleware('isAuthenticated')->group(function () {
        Route::prefix('transaction')->controller(TransactionController::class)->group(function () {
            Route::post('/{address}', 'create');
            Route::get('/{address}', 'index');
            Route::get('/{address}/{code}', 'show');
        });
    });    
    Route::put('/transaction-request', [TransactionController::class, 'request']);
    Route::get('/transaction-action/{code}', [TransactionController::class, 'action']);
});