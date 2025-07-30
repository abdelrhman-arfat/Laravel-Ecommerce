<?php

namespace App\Services\Interfaces;

interface JwtInterface
{
  public static function generateToken($user);
  public static function generateRefreshToken($user);
  public static function getUserFromToken($token);
  public static function isTokenValid($token);
  public static function refreshToken($token);
  public static function invalidateTokenInHeader();
  public static function invalidateTokenInCookie($token);
}
