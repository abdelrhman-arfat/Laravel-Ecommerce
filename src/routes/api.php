<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\VerificationController;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Support\Facades\Route;

//------------------------------ Auth -------------------------------

Route::post('/auth/signup', [AuthController::class, 'signup']);
Route::post('/auth/signin', [AuthController::class, 'signin']);

Route::get("/verify-email/{id}/{hash}", [VerificationController::class, 'verify'])->name('verification.verify');

Route::middleware(JwtMiddleware::class)->group(function () {});
