<?php

namespace App\Services;

use App\Helpers\DataOfPaginate;
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

  public static function successResponseForPagination($d, int $code = 200, string $message = '')
  {

    $data  = $d->items();
    $current_page = $d->currentPage();
    $last_page = $d->lastPage();
    $per_page = $d->perPage();
    $total = $d->total();

    return response()->json([
      'status' => 'success',
      'data' => $data,
      'current_page' => $current_page,
      'last_page' => $last_page,
      'per_page' => $per_page,
      'total' => $total,
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
