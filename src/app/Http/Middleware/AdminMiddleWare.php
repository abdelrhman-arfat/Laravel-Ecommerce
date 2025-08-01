<?php

namespace App\Http\Middleware;

use App\Services\JsonResponseService;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = auth()->user();
            if (!$user) $user = $request->user();
            if ($user->role != 'admin') return JsonResponseService::errorResponse(401, "You aren't admin");
        } catch (Exception $e) {
            return JsonResponseService::errorResponse(401, $e->getMessage());
        }
        return $next($request);
    }
}
