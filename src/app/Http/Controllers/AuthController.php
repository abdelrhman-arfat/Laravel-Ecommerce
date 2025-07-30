<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignUpUserRequest;
use App\Notifications\WelcomeEmail;
use App\Services\Interfaces\UserServiceInterface;
use App\Services\JsonResponseService;
use App\Services\JwtService;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    protected $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function signup(SignUpUserRequest $request)
    {
        try {

            $validated = $request->only("name", "email", "password");

            $user = $this->userService->create($validated);
            $user->sendEmailVerificationNotification();
            $user->notify(new WelcomeEmail());

            $token = JwtService::generateToken($user); // 1 hour
            $refreshToken = JwtService::generateRefreshToken($user); // 7 days

            return JsonResponseService::successResponse($user, 201, "User created successfully")
                ->withCookie(cookie(
                    'refresh_token',
                    $refreshToken,
                    60 * 24 * 7,
                    null,
                    null,
                    true, // secure: HTTPS only
                    true, // httpOnly
                    false, // raw
                    'Strict' // sameSite
                ))
                ->withCookie(cookie(
                    'token',
                    $token,
                    60,
                    null,
                    null,
                    true, // secure
                    true, // httpOnly
                    false,
                    'Strict'
                ));
        } catch (\Exception $e) {
            return JsonResponseService::errorResponse(500, $e->getMessage());
        }
    }
    public function signin(Request $request)
    {
        try {

            $credentials = $request->only('email', 'password');

            // Check if user exists
            $user = $this->userService->findByEmail($credentials['email']);
            if (!$user || !password_verify($credentials['password'], $user->password)) {
                return JsonResponseService::errorResponse(401, 'Invalid credentials');
            }

            $token = JwtService::generateToken($user); // 1 hour
            $refreshToken = JwtService::generateRefreshToken($user); // 7 days

            return JsonResponseService::successResponse($user, 200, "Login successful")
                ->withCookie(cookie(
                    'refresh_token',
                    $refreshToken,
                    60 * 24 * 7,
                    null,
                    null,
                    true, // secure
                    true, // httpOnly
                    false,
                    'Strict'
                ))
                ->withCookie(cookie(
                    'token',
                    $token,
                    60,
                    null,
                    null,
                    true,
                    true,
                    false,
                    'Strict'
                ));
        } catch (\Exception $e) {
            return JsonResponseService::errorResponse(500, $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        try {
            $refreshToken = $request->cookie('refresh_token');
            $accessToken = $request->cookie('token');
  
            if ($refreshToken) {
                JwtService::invalidateTokenInCookie($refreshToken);
            }
            if ($accessToken) {
                JwtService::invalidateTokenInCookie($accessToken);
            }

            return JsonResponseService::successResponse(null, 200, "Logout successful")
                ->withoutCookie("refresh_token")
                ->withoutCookie('token');
        } catch (\Exception $e) {
            return JsonResponseService::errorResponse(500, $e->getMessage());
        }
    }
}
