<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

//------------------------------ Auth -------------------------------

Route::post('/auth/signup', [AuthController::class, 'signup']);
Route::post('/auth/signin', [AuthController::class, 'signin']);
