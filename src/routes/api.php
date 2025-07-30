<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\VerificationController;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Support\Facades\Route;

//------------------------------ Without Middleware -------------------------------

// auth
Route::post('/auth/signup', [AuthController::class, 'signup']);
Route::post('/auth/signin', [AuthController::class, 'signin']);

// verification
Route::get("/verify-email/{id}/{hash}", [VerificationController::class, 'verify'])
  ->name('verification.verify')->middleware(['signed']);



//------------------------------ With Middleware -------------------------------

Route::middleware([JwtMiddleware::class])->group(function () {
  // auth
  Route::post('/auth/logout', [AuthController::class, 'logout']);
  //
});
