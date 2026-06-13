<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Meta\MetaAuthController;
use App\Http\Controllers\Campaign\CampaignController;
use App\Http\Controllers\Campaign\AdSetController;
use App\Http\Controllers\Campaign\AdController;
use App\Http\Controllers\Analytics\AnalyticsController;
use App\Http\Controllers\Billing\BillingController;
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
                Route::get('/',             [CampaignController::class, 'index']);
                Route::post('/',            [CampaignController::class, 'store']);
                Route::get('/{id}',         [CampaignController::class, 'show']);
                Route::put('/{id}',         [CampaignController::class, 'update']);
                Route::delete('/{id}',      [CampaignController::class, 'destroy']);
                Route::post('/{id}/toggle', [CampaignController::class, 'toggleStatus']);

                Route::post('/{campaignId}/ad-sets',             [AdSetController::class, 'store']);
                Route::put('/{campaignId}/ad-sets/{adSetId}',    [AdSetController::class, 'update']);
                Route::delete('/{campaignId}/ad-sets/{adSetId}', [AdSetController::class, 'destroy']);

                Route::post('/{campaignId}/ad-sets/{adSetId}/ads',          [AdController::class, 'store']);
                Route::put('/{campaignId}/ad-sets/{adSetId}/ads/{adId}',    [AdController::class, 'update']);
                Route::delete('/{campaignId}/ad-sets/{adSetId}/ads/{adId}', [AdController::class, 'destroy']);
            });

            // Analytics
            Route::prefix('analytics')->group(function () {
                Route::get('/dashboard',       [AnalyticsController::class, 'dashboard']);
                Route::get('/campaigns/{id}',  [AnalyticsController::class, 'campaign']);
                Route::get('/comparison',      [AnalyticsController::class, 'comparison']);
                Route::post('/seed-test-data', [AnalyticsController::class, 'seedTestData']);
            });

            // Billing
            Route::prefix('billing')->group(function () {
                Route::get('/plans',                        [BillingController::class, 'plans']);
                Route::get('/stats',                        [BillingController::class, 'stats']);
                Route::get('/invoices',                     [BillingController::class, 'invoices']);
                Route::post('/invoices',                    [BillingController::class, 'createInvoice']);
                Route::post('/invoices/{id}/pay',           [BillingController::class, 'recordPayment']);
                Route::post('/upgrade',                     [BillingController::class, 'upgradePlan']);
            });

            Route::middleware(['role:agency,super_admin'])->prefix('agency')->group(function () {});
            Route::middleware(['role:super_admin'])->prefix('admin')->group(function () {});
        });
    });
});

// Meta OAuth Callback - GET
Route::get('/v1/meta/callback', [App\Http\Controllers\Meta\MetaAuthController::class, 'handleCallback']);
