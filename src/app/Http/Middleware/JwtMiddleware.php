<?php

namespace App\Http\Middleware;

use App\Services\JsonResponseService;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $token = $request->cookie('token');

            if (!$token) {
                return JsonResponseService::errorResponse(400, "You aren't logged in");
            }
            JWTAuth::setToken($token);
            $user = JWTAuth::authenticate();
            $request->setUserResolver(fn() => $user);
        } catch (Exception $e) {
            return JsonResponseService::errorResponse(500, $e->getMessage());
        }
        return $next($request);
    }
}
