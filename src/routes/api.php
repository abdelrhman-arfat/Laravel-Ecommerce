<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Middleware\AdminMiddleWare;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Support\Facades\Route;

//------------------------------ Without Middleware -------------------------------

// auth
Route::post('/auth/signup', [AuthController::class, 'signup']);
Route::post('/auth/signin', [AuthController::class, 'signin']);

// verification
Route::get("/verify-email/{id}/{hash}", [VerificationController::class, 'verify'])
  ->name('verification.verify')->middleware(['signed']);

// products (accessible for all authenticated users)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/by-id/{id}', [ProductController::class, 'show']);

//------------------------------ With Middleware -------------------------------

Route::middleware([JwtMiddleware::class])->group(function () {
  // auth
  Route::post('/auth/logout', [AuthController::class, 'logout']);

  // cart
  Route::get('/carts', [CartController::class, 'index']);
  Route::post('/carts', [CartController::class, 'store']);
  Route::delete('/carts/{id}', [CartController::class, 'destroy']);
  Route::put('/carts/{id}', [CartController::class, 'update']);

  // admin-only routes
  Route::middleware(AdminMiddleWare::class)->group(function () {
    // users
    Route::get('/users', [UserController::class, 'getAllUsers']);
    Route::get('/users/email', [UserController::class, 'getUserByEmail']); // via query param ?email=...
    Route::get('/users/{id}', [UserController::class, 'getUserById']);
    Route::get('/users/{id}/verify', [UserController::class, 'verifyUser']);

    // products
    Route::get("/products/trashed", [ProductController::class, 'trashed']);
    Route::get("/products/all", [ProductController::class, 'allWithTrashed']);
    Route::get("/products/orders/{id}", [ProductController::class, "orders"]);
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::put("/products/{id}/restore", [ProductController::class, 'restore']); // restore soft-deleted product
    Route::delete('/products/{id}', [ProductController::class, 'destroy']); // soft-delete product

    // product variants
    Route::post('/variants', [ProductVariantController::class, 'store']);
    Route::put('/variants/{id}', [ProductVariantController::class, 'update']);
    Route::put('/variants/{id}/restore', [ProductVariantController::class, 'restore']);
    Route::delete('/variants/{id}', [ProductVariantController::class, 'destroy']);
  });
});
