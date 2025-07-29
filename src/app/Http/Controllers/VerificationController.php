<?php

namespace App\Http\Controllers;

use App\Services\JsonResponseService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;


class VerificationController extends Controller
{
    public function verify(Request $request, $id, $hash)
    {
        $user = auth()->user();

        if (!$user || $user->id != $id) {
            return JsonResponseService::errorResponse(401, 'Unauthorized');
        }

        if (!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return JsonResponseService::errorResponse(403, 'Invalid verification link');
        }

        if ($user->hasVerifiedEmail()) {
            return JsonResponseService::successResponse(null, 200, 'Email already verified');
        }

        $user->markEmailAsVerified();

        event(new Verified($user));

        return JsonResponseService::successResponse(null, 200, 'Email verified successfully');
    }
}
