<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DepositController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
});

// Public product routes
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{id}', [ProductController::class, 'show']);
    Route::get('/category/{categoryId}', [ProductController::class, 'getByCategory']);
    Route::get('/search/{query}', [ProductController::class, 'search']);
});

// Public category routes
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/{id}', [CategoryController::class, 'show']);
});

// Protected routes
Route::middleware('auth:api')->group(function () {
    
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::put('profile', [AuthController::class, 'updateProfile']);
        Route::put('change-password', [AuthController::class, 'changePassword']);
    });

    // User orders
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/', [OrderController::class, 'store']);
        Route::get('/{id}', [OrderController::class, 'show']);
        Route::put('/{id}/cancel', [OrderController::class, 'cancel']);
    });

    // Deposit routes
    Route::prefix('deposits')->group(function () {
        Route::post('/', [DepositController::class, 'store']); // Create deposit request
        Route::get('/user', [DepositController::class, 'userDeposits']); // User's deposit history
    });

    // Admin routes
    Route::middleware(['role:admin|subadmin'])->prefix('admin')->group(function () {
        
        // Product management
        Route::prefix('products')->group(function () {
            Route::post('/', [ProductController::class, 'store']);
            Route::put('/{id}', [ProductController::class, 'update']);
            Route::delete('/{id}', [ProductController::class, 'destroy']);
            Route::post('/{id}/images', [ProductController::class, 'uploadImages']);
        });

        // Category management
        Route::prefix('categories')->group(function () {
            Route::post('/', [CategoryController::class, 'store']);
            Route::put('/{id}', [CategoryController::class, 'update']);
            Route::delete('/{id}', [CategoryController::class, 'destroy']);
        });

        // Order management
        Route::prefix('orders')->group(function () {
            Route::get('/all', [OrderController::class, 'adminIndex']);
            Route::put('/{id}/status', [OrderController::class, 'updateStatus']);
            Route::put('/{id}/payment-status', [OrderController::class, 'updatePaymentStatus']);
        });

        // User management
        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index']);
            Route::post('/', [UserController::class, 'store']);
            Route::get('/{id}', [UserController::class, 'show']);
            Route::put('/{id}', [UserController::class, 'update']);
            Route::delete('/{id}', [UserController::class, 'destroy']);
            Route::put('/{id}/status', [UserController::class, 'updateStatus']);
        });

        // Deposit management (Admin only)
        Route::prefix('deposits')->group(function () {
            Route::get('/', [DepositController::class, 'index']); // Get all deposits
            Route::put('/{id}', [DepositController::class, 'update']); // Update deposit status
        });

        // Reports and Analytics
        Route::prefix('reports')->group(function () {
            Route::get('/dashboard', [OrderController::class, 'dashboardStats']);
            Route::get('/sales', [OrderController::class, 'salesReport']);
            Route::get('/products', [ProductController::class, 'productReport']);
        });
    });
});
