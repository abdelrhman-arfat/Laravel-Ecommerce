<?php

namespace App\Services\Interfaces;

interface PaymentInterface
{
  public function create(string $paymentSession);
  public function find(string $paymentSession);
}
