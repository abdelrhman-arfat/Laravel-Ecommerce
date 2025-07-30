<?php

namespace App\Services\Interfaces;

interface JwtInterface
{
  public static function generateToken($user);
  public static function generateRefreshToken($user);
  public static function getUserFromToken();
  public static function isTokenValid();
  public static function refreshToken();
  public static function invalidateTokenInHeader();
  public static function invalidateTokenInCookie($token);
}
