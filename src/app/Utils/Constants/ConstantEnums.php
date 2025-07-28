<?php

namespace App\Utils\Constants;

class ConstantEnums
{
  public static function colors(): array
  {
    return [
      'red'    => 'red',
      'green'  => 'green',
      'blue'   => 'blue',
      'black'  => 'black',
      'white'  => 'white',
      'brown'  => 'brown',
      'orange' => 'orange',
    ];
  }

  public static function sizes(): array
  {
    return [
      'S'    => 'S',
      'M'    => 'M',
      'L'    => 'L',
      'XL'   => 'XL',
      'XXL'  => 'XXL',
      'XXXL' => 'XXXL',
    ];
  }

  public static function statuses(): array
  {
    return [
      'pending'   => 'pending',
      'completed' => 'completed',
      'cancelled' => 'cancelled',
    ];
  }

  public static function roles(): array
  {
    return [
      'admin' => 'admin',
      'user'  => 'user',
    ];
  }
}
