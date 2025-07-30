<?php

namespace App\Services;

use App\Services\Interfaces\JwtInterface;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtService implements JwtInterface
{
  /**
   * Generate token for the given user (Access Token).
   */
  public static function generateToken($user): string
  {
    $ttl = config('jwt.ttl'); // in minutes
    $claims = [
      'exp' => now()->addMinutes($ttl)->timestamp,
      'type' => 'access',
    ];
    return JWTAuth::claims($claims)->fromUser($user);
  }

  /**
   * Generate Refresh Token.
   */
  public static function generateRefreshToken($user): string
  {
    $refreshTtl = config('jwt.refresh_ttl'); // in minutes
    $claims = [
      'exp' => now()->addMinutes($refreshTtl)->timestamp,
      'type' => 'refresh',
    ];
    return JWTAuth::claims($claims)->fromUser($user);
  }

  /**
   * Get the currently authenticated user from token.
   */
  public static function getUserFromToken()
  {
    try {
      return JWTAuth::parseToken()->authenticate();
    } catch (JWTException $e) {
      return null;
    }
  }

  /**
   * Invalidate the current token (logout).
   */
  public static function invalidateTokenInHeader(): bool
  {
    try {
      JWTAuth::parseToken()->invalidate();
      return true;
    } catch (JWTException $e) {
      return false;
    }
  }
  public static function invalidateTokenInCookie($token)
  {
    JWTAuth::setToken($token)->invalidate();
  }

  /**
   * Refresh the token.
   */
  public static function refreshToken(): ?string
  {
    try {
      return JWTAuth::parseToken()->refresh();
    } catch (JWTException $e) {
      return null;
    }
  }

  /**
   * Check if token is valid.
   */
  public static function isTokenValid(): bool
  {
    try {
      return JWTAuth::parseToken()->check();
    } catch (JWTException $e) {
      return false;
    }
  }
}
