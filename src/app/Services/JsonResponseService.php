<?php

namespace App\Services;

use App\Helpers\DataOfPaginate;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;

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
    if (is_array($d) && isset($d['data']) && isset($d['total'])) {
      $request = request();
      $page = (int) $request->query('page', 1);
      $perPage = (int) $request->query('limit', 10);

      $d = new LengthAwarePaginator(
        $d['data'],
        $d['total'],
        $perPage,
        $page,
        ['path' => url()->current(), 'query' => $request->query()]
      );
    }

    return response()->json([
      "message" => $message,
      'status' => 'success',
      'data' => $d->items(),
      'meta' => [
        'current_page' => $d->currentPage(),
        'last_page' => $d->lastPage(),
        'total' => $d->total(),
        'per_page' => $d->perPage(),
      ],
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
