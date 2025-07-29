<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

class JsonResponseService
{
  /**
   * Return a success JSON response.
   */
  public static function successResponse(mixed $data = null, int $code = 200, string $message = ''): JsonResponse
  {
    return response()->json([
      'status' => 'success',
      'data' => $data,
      'message' => $message,
    ], $code);
  }

  /**
   * Return an error JSON response.
   */
  public static function errorResponse(int $code = 400, string $message = '', mixed $data = null): JsonResponse
  {
    return response()->json([
      'status' => 'error',
      'data' => $data,
      'message' => $message,
    ], $code);
  }
}
