<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\ProductController;
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
Route::get('/products/{id}', [ProductController::class, 'show']);

//------------------------------ With Middleware -------------------------------

Route::middleware([JwtMiddleware::class])->group(function () {
  // auth
  Route::post('/auth/logout', [AuthController::class, 'logout']);

  // products (accessible for all authenticated users)
  Route::get('/products', [ProductController::class, 'index']);
  Route::get('/products/{id}', [ProductController::class, 'show']);

  // admin-only routes
  Route::middleware(AdminMiddleWare::class)->group(function () {
    // users
    Route::get('/users', [UserController::class, 'getAllUsers']);
    Route::get('/users/email', [UserController::class, 'getUserByEmail']); // via query param ?email=...
    Route::get('/users/{id}', [UserController::class, 'getUserById']);
    Route::get('/users/{id}/verify', [UserController::class, 'verifyUser']);

    // products
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::put("/products/{id}/restore", [ProductController::class, 'restore']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
  });
});
