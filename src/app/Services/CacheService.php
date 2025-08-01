<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{

  public static function key($tableName, $filters)
  {
    return  $tableName . "_" . md5(json_encode($filters));
  }
  public static function get(string $key)
  {
    return Cache::get($key);
  }
  public static function forget(string $key)
  {
    return Cache::forget($key);
  }

  public static function has(string $key)
  {
    return Cache::has($key);
  }
  public static function remember(string $key, int $ttl = 15, \Closure $callback)
  {
    return Cache::remember($key, now()->addMinutes($ttl), $callback);
  }
}
