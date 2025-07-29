<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\UserServiceInterface;
use App\Services\JsonResponseService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;


class VerificationController extends Controller
{
    protected $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function verify(Request $request, $id, $hash)
    {
        $user = $this->userService->find($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return JsonResponseService::errorResponse(401, 'Invalid token');
        }

        if ($user->hasVerifiedEmail()) {
            return JsonResponseService::successResponse($user, 200, "Email already verified");
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return JsonResponseService::successResponse($user, 201, "Email verified successfully");
    }
}
