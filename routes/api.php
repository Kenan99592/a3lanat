<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Meta\MetaAuthController;
use App\Http\Controllers\Campaign\CampaignController;
use App\Http\Controllers\Campaign\AdSetController;
use App\Http\Controllers\Campaign\AdController;
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

            // Meta
            Route::prefix('meta')->group(function () {
                Route::get('/auth-url',               [MetaAuthController::class, 'getAuthUrl']);
                Route::post('/callback',              [MetaAuthController::class, 'callback']);
                Route::get('/accounts',               [MetaAuthController::class, 'accounts']);
                Route::delete('/accounts/{id}',       [MetaAuthController::class, 'disconnect']);
                Route::post('/accounts/{id}/refresh', [MetaAuthController::class, 'refreshAccounts']);
            });

            // Campaigns
            Route::prefix('campaigns')->group(function () {
                Route::get('/',                [CampaignController::class, 'index']);
                Route::post('/',               [CampaignController::class, 'store']);
                Route::get('/{id}',            [CampaignController::class, 'show']);
                Route::put('/{id}',            [CampaignController::class, 'update']);
                Route::delete('/{id}',         [CampaignController::class, 'destroy']);
                Route::post('/{id}/toggle',    [CampaignController::class, 'toggleStatus']);

                // Ad Sets
                Route::post('/{campaignId}/ad-sets',                      [AdSetController::class, 'store']);
                Route::put('/{campaignId}/ad-sets/{adSetId}',             [AdSetController::class, 'update']);
                Route::delete('/{campaignId}/ad-sets/{adSetId}',          [AdSetController::class, 'destroy']);

                // Ads
                Route::post('/{campaignId}/ad-sets/{adSetId}/ads',                   [AdController::class, 'store']);
                Route::put('/{campaignId}/ad-sets/{adSetId}/ads/{adId}',             [AdController::class, 'update']);
                Route::delete('/{campaignId}/ad-sets/{adSetId}/ads/{adId}',          [AdController::class, 'destroy']);
            });

            Route::middleware(['role:agency,super_admin'])->prefix('agency')->group(function () {});
            Route::middleware(['role:super_admin'])->prefix('admin')->group(function () {});
        });
    });
});
