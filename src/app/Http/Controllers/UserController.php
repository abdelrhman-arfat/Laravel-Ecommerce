<?php

namespace App\Http\Controllers;

use App\Http\Requests\FindUserByEmailRequest;
use App\Services\Interfaces\UserServiceInterface;
use App\Services\JsonResponseService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function getMe(Request $request)
    {
        try {
            $user = $request->user();
            return JsonResponseService::successResponse($user, 200, "Authenticated user retrieved");
        } catch (\Exception $e) {
            return JsonResponseService::errorResponse(500, $e->getMessage());
        }
    }

    public function getAllUsers()
    {
        try {
            $users = $this->userService->all();
            return JsonResponseService::successResponse($users, 200, "All users retrieved successfully");
        } catch (\Exception $e) {
            return JsonResponseService::errorResponse(500, $e->getMessage());
        }
    }

    public function getUserByEmail(FindUserByEmailRequest $request)
    {
        try {
            $email = $request->email;
            $user = $this->userService->findByEmail($email);
            if (!$user) return JsonResponseService::errorResponse(404, "User not found");
            return JsonResponseService::successResponse($user, 200, "User found by email");
        } catch (\Exception $e) {
            return JsonResponseService::errorResponse(500, $e->getMessage());
        }
    }

    public function getUserById($id)
    {
        try {
            $user = $this->userService->find($id);
            if (!$user) return JsonResponseService::errorResponse(404, "User not found");
            return JsonResponseService::successResponse($user, 200, "User retrieved by ID");
        } catch (\Exception $e) {
            return JsonResponseService::errorResponse(500, $e->getMessage());
        }
    }

    public function verifyUser($id)
    {
        try {
            $user = $this->userService->find($id);
            if (!$user) return JsonResponseService::errorResponse(404, "User not found");
            $verified = $this->userService->verify($user);

            return JsonResponseService::successResponse($verified, 200, "User verified successfully");
        } catch (\Exception $e) {
            return JsonResponseService::errorResponse(500, $e->getMessage());
        }
    }
}
