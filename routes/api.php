<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\TransactionController;
use App\Http\Controllers\API\V1\ProductController;
use App\Http\Controllers\API\V1\CategoryController;
use App\Http\Controllers\API\V1\ReportController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('v1')->group(function () {
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/user', [AuthController::class, 'profile']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        Route::middleware('role:cashier,admin')->group(function () {
            Route::apiResource('/products', ProductController::class)->only(['index', 'show']);
            Route::get('/products/barcode/{barcode}', [ProductController::class, 'show']);
            Route::apiResource('/categories', CategoryController::class)->only(['index', 'show']);
            Route::apiResource('/transactions', TransactionController::class)->only(['index', 'show', 'store']);
        });

        Route::middleware('role:admin')->group(function () {
            Route::apiResource('/products', ProductController::class)->except(['index', 'show']);
            Route::apiResource('/transactions', TransactionController::class)->only(['update', 'destroy']);
            Route::apiResource('/categories', CategoryController::class)->except(['index', 'show']);
            Route::get('/reports/summary', [ReportController::class, 'summary']);
            Route::get('/reports/sales', [ReportController::class, 'salesReport']);
        });
    });
});
