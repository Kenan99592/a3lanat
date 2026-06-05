<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login',    [AuthController::class, 'login']);
    });

    Route::middleware(['auth:sanctum'])->group(function () {

        Route::prefix('auth')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me',      [AuthController::class, 'me']);
        });

        Route::middleware(['tenant'])->group(function () {

            Route::middleware(['role:client,agency,super_admin'])->group(function () {
                // الطبقة 2 وما بعدها
            });

            Route::middleware(['role:agency,super_admin'])->prefix('agency')->group(function () {
                // إدارة العملاء
            });

            Route::middleware(['role:super_admin'])->prefix('admin')->group(function () {
                // إدارة المنصة
            });
        });
    });
});
